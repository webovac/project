<?php

declare(strict_types=1);

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
	public function __construct(private ImageStorage $imageStorage, private Dir $dir)
	{}


	/** @throws ImageExtensionException */
	public function upload(FileUpload $upload, string $namespace = 'cms'): string
	{
		$content = file_get_contents($upload->getTemporaryFile());
		$checksum = sha1_file($upload->getTemporaryFile());
		[$path, $identifier] = $this->getSavePath(
			Strings::webalize($this->getSanitizedName($upload), '._'),
			$namespace,
			$checksum
		);
		file_put_contents($path, $content, LOCK_EX);
		return $identifier;
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


	/** @throws ImageExtensionException */
	private function getSavePath(string $name, string $namespace, string $checksum): array
	{
		ini_set('memory_limit', '256M');
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
			if (sha1_file($path) === $checksum) {
				break;
			}
			$name = (!isset($i) && ($i = 2)) ? $name . '.' . $i : substr($name, 0, -(2 + (int) floor(log($i - 1, 10)))) . '.' . $i;
			$i++;
		}
		$identifier = implode('/', [$namespace, $prefix, $name . $extension]);
		return [$path, $identifier];
	}


	private function getSanitizedName(FileUpload $upload): string
	{
		$name = Strings::webalize($upload->name, '.', lower: false);
		$name = str_replace(['-.', '.-'], '.', $name);
		$name = trim($name, '.-');
		$name = $name === '' ? 'unknown' : $name;
		if ($ext = $this->getSuggestedExtension($upload)) {
			$name = preg_replace('#\.[^.]+$#D', '', $name);
			$name .= '.' . $ext;
		}

		return $name;
	}


	private function getSuggestedExtension(FileUpload $upload): ?string
	{
		if ($upload->isOk()) {
			$exts = finfo_file(finfo_open(FILEINFO_EXTENSION), $upload->getTemporaryFile());
			if ($exts && $exts !== '???') {
				return preg_replace('~[/,].*~', '', $exts);
			}
			[, , $type] = @getimagesize($upload->getTemporaryFile()); // @ - files smaller than 12 bytes causes read error
			if ($type) {
				return image_type_to_extension($type, false);
			}
		}
		return null;
	}
}
