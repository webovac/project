<?php

declare(strict_types=1);

namespace App\Lib;

use Nette\Utils\Strings;
use Nextras\Dbal\QueryBuilder\QueryBuilder;
use Nextras\Orm\Collection\Functions\IQueryBuilderFunction;
use Nextras\Orm\Collection\Helpers\DbalExpressionResult;
use Nextras\Orm\Collection\Helpers\DbalQueryBuilderHelper;


class OrmFunctions
{
	public const string LIKE_FILTER = 'likeFilter';
	public const string PERSON_FILTER = 'personFilter';
	public const string PERSON_ORDER = 'personOrder';
	public const array CUSTOM_FUNCTIONS = [
		self::LIKE_FILTER => true,
		self::PERSON_FILTER => true,
		self::PERSON_ORDER => true,
	];


	public static function call(string $name)
	{
		return new class($name) implements IQueryBuilderFunction {
			public function __construct(private $name)
			{}

			public function processQueryBuilderExpression(DbalQueryBuilderHelper $helper, QueryBuilder $builder, array $args): DbalExpressionResult
			{
				return OrmFunctions::{$this->name}($helper, $builder, $args);
			}
		};
	}


	public static function likeFilter(DbalQueryBuilderHelper $helper, QueryBuilder $builder, array $args): DbalExpressionResult
	{
		assert(count($args) === 2 && is_string($args[0]) && is_string($args[1]));
		$column = $helper->processPropertyExpr($builder, $args[0])->args[1];
		return new DbalExpressionResult(['LOWER(%column) LIKE %_like_', $column, Strings::lower($args[1])]);
	}


	public static function personFilter(DbalQueryBuilderHelper $helper, QueryBuilder $builder, array $args): DbalExpressionResult
	{
		assert(count($args) === 1 && is_string($args[0]));
		return new DbalExpressionResult([
			'LOWER(last_name || \' \' || first_name) LIKE %_like_',
			Strings::lower($args[0])
		]);
	}


	public static function personOrder(DbalQueryBuilderHelper $helper, QueryBuilder $builder, array $args): DbalExpressionResult
	{
		$lastNameColumn = $helper->processPropertyExpr($builder, $args[1])->args[1];
		$firstNameColumn = $helper->processPropertyExpr($builder, $args[0])->args[1];
		return new DbalExpressionResult(['%column || %column', $lastNameColumn, $firstNameColumn]);
	}
}
