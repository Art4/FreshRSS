<?php
declare(strict_types=1);

final class CacheServiceTest extends PHPUnit\Framework\TestCase
{
	public function testCacheServiceExists(): void {
		self::assertTrue(class_exists(FreshRSS_Cache_Service::class));
	}

	public function testCacheServiceCanBeCreated(): void {
		self::assertInstanceOf(FreshRSS_Cache_Service::class, new FreshRSS_Cache_Service(''));
	}
}
