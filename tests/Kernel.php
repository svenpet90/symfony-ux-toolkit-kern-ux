<?php

declare(strict_types=1);

namespace KernUx\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\UX\TwigComponent\TwigComponentBundle;
use Twig\Extra\TwigExtraBundle\TwigExtraBundle;

/**
 * Minimal kernel that boots just enough of Symfony to render the kit's Twig
 * components exactly as a real application would (TwigComponent + html_cva).
 *
 * Every component lives in its own `<component>/templates/components` folder.
 * They are all registered under Twig's main namespace so that
 * `<twig:Button>` resolves to `components/Button.html.twig` regardless of which
 * component folder it lives in.
 */
final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new TwigComponentBundle(),
            new TwigExtraBundle(),
        ];
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/kern-ux-toolkit-tests/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/kern-ux-toolkit-tests/log';
    }

    private function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'secret' => 'kern-ux-toolkit-tests',
            'test' => true,
            'http_method_override' => false,
            'handle_all_throwables' => true,
        ]);

        $paths = [];
        foreach (glob($this->getProjectDir().'/*/templates', \GLOB_ONLYDIR) as $dir) {
            $paths[$dir] = null; // main namespace -> `components/<Name>.html.twig`
        }

        $container->extension('twig', [
            'default_path' => $this->getProjectDir().'/tests/templates',
            'paths' => $paths,
            // Kept strict even outside debug so undefined variables surface as
            // errors, and uncached so template edits are always re-evaluated.
            'strict_variables' => true,
            'cache' => false,
        ]);

        $container->extension('twig_component', [
            'anonymous_template_directory' => 'components',
            // The kit ships only anonymous (class-less) components, but the
            // bundle still requires a defaults map; this namespace is unused.
            'defaults' => [
                'KernUx\\Components\\' => 'components/',
            ],
        ]);
    }

    private function configureRoutes(RoutingConfigurator $routes): void
    {
    }
}
