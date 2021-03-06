<?php

declare(strict_types=1);

namespace Jon\PathHelper\Tests;

use Jon\Path;
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
        self::assertEquals('/foo/bar/baz/', PathHelper::path('/foo/././bar/./baz/'));
        self::assertEquals('foo/bar/baz', PathHelper::path('./foo/././bar/./baz'));
        self::assertEquals('/foo/bar/baz', PathHelper::path('/./foo/././bar/./baz'));
    }

    public function testResolveParentReferences(): void
    {
        self::assertEquals('foo/bar/baz', PathHelper::path('foo/', '/bar/../bar/baz/../', '/baz'));
    }

    public function testCanResolveMultipleParentReferences(): void
    {
        self::assertEquals('foo/baz', PathHelper::path('foo/bar/baz', '../../', '/baz'));
        self::assertEquals('foo/', PathHelper::path('foo/bar/baz', '../../'));
        self::assertEquals('foo/', PathHelper::path('foo/bar/baz', '../', '../'));
        self::assertEquals('/', PathHelper::path('foo/bar/baz', '../../../'));
        self::assertEquals('../', PathHelper::path('foo/bar/baz', '../../../../'));
        self::assertEquals('../..', PathHelper::path('foo/bar/baz', '../../../../..'));
    }

    public function testIgnoreLeadingParentReference(): void
    {
        self::assertEquals('../foo/bar/baz', PathHelper::path('../foo/bar/../bar/baz/../baz'));
        self::assertEquals('/../foo/bar/baz', PathHelper::path('/../foo/bar/../bar/baz/../baz'));
    }

    public function testFilename(): void
    {
        self::assertEquals('foo/bar/baz.ico', PathHelper::path('foo/bar', 'baz.ico'));
        self::assertEquals('foo/bar/baz.ico', PathHelper::path('foo/bar/', 'baz.ico'));
        self::assertEquals('foo/bar/baz.ico', PathHelper::path('foo/bar', '//baz.ico'));
        self::assertEquals('foo/bar/baz.ico', PathHelper::path('foo/bar/', '//baz.ico'));
        self::assertEquals('foo/bar/baz.ico', PathHelper::path('foo/bar', './baz.ico'));
        self::assertEquals('foo/bar/baz.ico', PathHelper::path('foo/bar/', './baz.ico'));
    }

    public function testUseCase(): void
    {
        $projectDir = '/user/projects/my-project/src/';
        $assetsDir = '../public/assets/';
        $favicon = './favicon.ico';

        self::assertEquals('/user/projects/my-project/public/assets/favicon.ico', PathHelper::path($projectDir, $assetsDir, $favicon));
    }

    public function testAlias(): void
    {
        $projectDir = '/user/projects/my-project/src/';
        $assetsDir = '../public/assets/';
        $favicon = './favicon.ico';

        self::assertEquals('/user/projects/my-project/public/assets/favicon.ico', Path::combine($projectDir, $assetsDir, $favicon));
    }
}
