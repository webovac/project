<?php

namespace App\Lib;

use Contributte\ImageStorage\Exception\ImageExtensionException;
use Contributte\ImageStorage\Exception\ImageResizeException;
use Contributte\ImageStorage\ImageStorage;
use Nette\Http\FileUpload;
use Nette\Utils\Strings;
use Stepapo\Utils\Service;
use Webovac\Core\Lib\Dir;
use Webovac\Core\Lib\FileUploader;


class AppFileUploader implements FileUploader, Service
{
	public const ALGORITHM_CONTENT = 'sha1';


	public function __construct(private ImageStorage $imageStorage, private Dir $dir)
	{}


	/** @throws ImageExtensionException */
	public function upload(FileUpload $upload, string $namespace = 'cms'): string
	{
		return $upload->isImage() ? $this->uploadImage($upload, $namespace) : $this->uploadFile($upload, $namespace);
	}


	public function delete(string $identifier): void
	{
		$this->imageStorage->delete($identifier);
	}

	/** @throws ImageResizeException */
	public function getResponse(FileUpload $upload, string $namespace = 'cms'): array
	{
		return [
			'uploaded' => 1,
			'fileName' => $upload->getSanitizedName(),
			'url' => '/' . $this->imageStorage->fromIdentifier($this->upload($upload, $namespace))->createLink(),
		];
	}


	/** @throws ImageResizeException */
	public function getPath(string $identifier, ?string $size = null, ?string $flag = null, ?int $quality = null): string
	{
		return $size
			? $this->imageStorage->fromIdentifier([$identifier, $size, $flag, $quality])->getPath()
			: $this->imageStorage->fromIdentifier($identifier)->getPath();
	}


	/** @throws ImageResizeException */
	public function getUrl(string $identifier, ?string $size = null, ?string $flag = null, ?int $quality = null): string
	{
		return $size
			? $this->imageStorage->fromIdentifier([$identifier, $size, $flag, $quality])->createLink()
			: $this->imageStorage->fromIdentifier($identifier)->createLink();
	}


	private function uploadImage(FileUpload $upload, string $namespace): string
	{
		$savedFile = $this->imageStorage->saveContent(file_get_contents($upload->getTemporaryFile()), $upload->getSanitizedName(), $namespace);
		return $savedFile->identifier;
	}


	/** @throws ImageExtensionException */
	private function uploadFile(FileUpload $upload, string $namespace): string
	{
		$content = file_get_contents($upload->getTemporaryFile());
		$checksum = call_user_func_array(self::ALGORITHM_CONTENT, [$content]);
		[$path, $identifier] = $this->getSavePath(
			Strings::webalize($upload->getSanitizedName(), '._'),
			$namespace,
			$checksum
		);
		file_put_contents($path, $content, LOCK_EX);
		return $identifier;
	}


	/** @throws ImageExtensionException */
	private function getSavePath(string $name, string $namespace, string $checksum): array
	{
		$prefix = substr($checksum, 0, 2);
		$dir = implode('/', [$this->dir->getWwwDir() . '/data', $namespace, $prefix]);
		@mkdir($dir, 0775, true);
		preg_match('/(.*)(\.[^\.]*)/', $name, $matches);
		if (!$matches[2]) {
			throw new ImageExtensionException(sprintf('Error defining image extension (%s)', $name));
		}
		$name = $matches[1];
		$extension = $matches[2];
		while (file_exists($path = $dir . '/' . $name . $extension)) {
			if (call_user_func_array(self::ALGORITHM_CONTENT, [file_get_contents($path)]) === $checksum) {
				break;
			}
			$name = (!isset($i) && ($i = 2)) ? $name . '.' . $i : substr($name, 0, -(2 + (int) floor(log($i - 1, 10)))) . '.' . $i;
			$i++;
		}
		$identifier = implode('/', [$namespace, $prefix, $name . $extension]);
		return [$path, $identifier];
	}
}
