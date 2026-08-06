<?php

declare(strict_types=1);

namespace KernUx\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

/**
 * Boots the kit through a real Symfony + TwigComponent stack and:
 *  - compiles every component template (catches syntax / unknown-function bugs),
 *  - renders every example (catches runtime rendering bugs),
 *  - asserts the KERN markup produced for a few representative components.
 */
final class ComponentRenderTest extends KernelTestCase
{
    private const ROOT = __DIR__.'/..';

    protected function tearDown(): void
    {
        parent::tearDown();

        // Booting the kernel registers a Symfony exception handler that is not
        // removed on shutdown; restore it so PHPUnit does not flag the test as
        // risky ("did not remove its own exception handlers").
        restore_exception_handler();
    }

    private static function twig(): Environment
    {
        self::bootKernel();

        return self::getContainer()->get('twig');
    }

    /**
     * Compiling a template exercises its syntax and every Twig function/filter
     * it references (e.g. `html_cva`), without needing to supply props.
     */
    #[DataProvider('componentTemplates')]
    public function testComponentTemplateCompiles(string $twigName): void
    {
        self::twig()->load($twigName);

        $this->addToAssertionCount(1);
    }

    #[DataProvider('exampleTemplates')]
    public function testExampleRenders(string $file): void
    {
        $html = self::twig()->createTemplate((string) file_get_contents($file))->render();

        self::assertNotSame('', trim($html), 'Example rendered empty output');
    }

    public function testButtonRendersVariantAndSize(): void
    {
        $html = $this->render('<twig:Button variant="secondary" size="x-small">Klick</twig:Button>');

        self::assertStringContainsString('class="kern-btn kern-btn--secondary kern-btn--x-small"', $html);
        self::assertStringContainsString('<span class="kern-label">Klick</span>', $html);
    }

    public function testHeadingRendersSizeClass(): void
    {
        $html = $this->render('<twig:Heading size="x-large" as="h1">Titel</twig:Heading>');

        self::assertStringContainsString('<h1 class="kern-heading-x-large"', $html);
    }

    public function testBadgeRendersVariantAndIcon(): void
    {
        $html = $this->render('<twig:Badge variant="info" icon="info">Info</twig:Badge>');

        self::assertStringContainsString('kern-badge kern-badge--info', $html);
        self::assertStringContainsString('<span class="kern-icon kern-icon--info" aria-hidden="true"></span>', $html);
    }

    public function testTextInputRendersFullErrorState(): void
    {
        $html = $this->render('<twig:Text name="familienname" label="Familienname" hint="Wie im Ausweis" error="Pflichtfeld" />');

        self::assertStringContainsString('kern-form-input kern-form-input--error', $html);
        self::assertStringContainsString('kern-form-input__input--error', $html);
        self::assertStringContainsString('aria-invalid="true"', $html);
        self::assertStringContainsString('aria-describedby="familienname-hint familienname-error"', $html);
        self::assertStringContainsString('<p class="kern-error" id="familienname-error">', $html);
    }

    public function testTableSubComponentsResolve(): void
    {
        $html = $this->render(<<<'TWIG'
            <twig:Table title="Zeiten">
                <twig:Table:Body>
                    <twig:Table:Row>
                        <twig:Table:Cell variant="numeric">08:00</twig:Table:Cell>
                    </twig:Table:Row>
                </twig:Table:Body>
            </twig:Table>
            TWIG);

        self::assertStringContainsString('<table class="kern-table"', $html);
        self::assertStringContainsString('<caption class="kern-title">Zeiten</caption>', $html);
        self::assertStringContainsString('class="kern-table__cell kern-table__cell--numeric"', $html);
    }

    public function testLinkUsesRenamedSmallModifier(): void
    {
        $html = $this->render('<twig:Link href="#" size="small" noVisitedState="true">Klein</twig:Link>');

        // KERN-UX 2.7 renamed the `x-small` link modifier to `small`.
        self::assertStringContainsString('kern-link--small', $html);
        self::assertStringNotContainsString('kern-link--x-small', $html);
        self::assertStringContainsString('kern-link--no-visited-state', $html);
    }

    public function testSummaryPlacesNumberInsideTitleWrapper(): void
    {
        $html = $this->render('<twig:Summary number="1" title="Angaben zur Person" id="s1" />');

        // KERN-UX 2.7 moved kern-number into the h3 (kern-summary__title-wrapper).
        self::assertMatchesRegularExpression(
            '#<h3 class="kern-title kern-title--small kern-summary__title-wrapper" id="s1">\s*<span class="kern-number">1</span><span>Angaben zur Person</span>#',
            $html,
        );
    }

    public function testTaskListItemUsesContentWrapper(): void
    {
        $html = $this->render(<<<'TWIG'
            <twig:TaskList title="Antrag">
                <twig:TaskList:Item number="1" title="Angaben zur Person" id="t1">
                    <twig:Badge variant="success" size="small">Erledigt</twig:Badge>
                </twig:TaskList:Item>
            </twig:TaskList>
            TWIG);

        // KERN-UX 2.7 wraps the number + text inside the link (kern-task-list__content-wrapper).
        self::assertStringContainsString('kern-link kern-link--stretched kern-task-list__content-wrapper', $html);
        self::assertMatchesRegularExpression(
            '#kern-task-list__content-wrapper"[^>]*>\s*<span class="kern-number">1</span><span>Angaben zur Person</span>#',
            $html,
        );
    }

    public function testHgroupRenders(): void
    {
        $html = $this->render('<twig:Hgroup><twig:Title>Titel</twig:Title></twig:Hgroup>');

        self::assertStringContainsString('<hgroup class="kern-hgroup', $html);
    }

    private function render(string $template): string
    {
        return self::twig()->createTemplate($template)->render();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function componentTemplates(): array
    {
        $cases = [];
        foreach (glob(self::ROOT.'/*/templates/components/*.html.twig') ?: [] as $file) {
            $cases[self::twigName($file)] = [self::twigName($file)];
        }
        foreach (glob(self::ROOT.'/*/templates/components/*/*.html.twig') ?: [] as $file) {
            $cases[self::twigName($file)] = [self::twigName($file)];
        }

        return $cases;
    }

    /**
     * @return array<string, array{string}>
     */
    public static function exampleTemplates(): array
    {
        $cases = [];
        foreach (glob(self::ROOT.'/*/examples/*.html.twig') ?: [] as $file) {
            $component = basename(\dirname($file, 2));
            $cases[$component.'/'.basename($file)] = [$file];
        }

        return $cases;
    }

    private static function twigName(string $file): string
    {
        $file = str_replace('\\', '/', $file);
        $marker = '/templates/';

        return substr($file, strrpos($file, $marker) + \strlen($marker));
    }
}
