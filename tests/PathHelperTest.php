<?php

declare(strict_types=1);

namespace Jon\PathHelper\Tests;

use Jon\PathHelper;
use PHPUnit\Framework\TestCase;

class PathHelperTest extends TestCase
{
    public function testCanCreateSimplePath(): void
    {
        self::assertEquals('foo/bar/baz', PathHelper::path('foo', 'bar', 'baz'));
        self::assertEquals('foo/bar/baz', PathHelper::path('foo/bar/baz'));
    }

    public function testCanCreatePathWithoutArguments(): void
    {
        self::assertEquals('', PathHelper::path());
    }

    public function testCanCreateSimplePathWithLeadingSlash(): void
    {
        self::assertEquals('/foo/bar/baz', PathHelper::path('/foo', 'bar', 'baz'));
    }

    public function testCanCreateSimplePathWithTrailingSlash(): void
    {
        self::assertEquals('foo/bar/baz/', PathHelper::path('foo', 'bar', 'baz/'));
        self::assertEquals('/foo/bar/baz/', PathHelper::path('/foo/bar/baz/'));
    }

    public function testRemovalUnnecessarySlashes(): void
    {
        self::assertEquals('foo/bar/baz', PathHelper::path('foo/', '/bar//', '/baz'));
    }

    public function testRemovalCurrentDirectoryReference(): void
    {
        self::assertEquals('foo/bar/baz', PathHelper::path('foo/././bar/./baz'));
        self::assertEquals('/foo/bar/baz', PathHelper::path('/foo/././bar/./baz'));
    }

    public function testResolveParentReferences(): void
    {
        self::assertEquals('foo/bar/baz', PathHelper::path('foo/', '/bar/../bar/baz/../', '/baz'));
    }

    public function testIgnoreLeadingParentReference(): void
    {
        self::assertEquals('../foo/bar/baz', PathHelper::path('../foo/bar/../bar/baz/../baz'));
        self::assertEquals('/../foo/bar/baz', PathHelper::path('/../foo/bar/../bar/baz/../baz'));
    }
}
