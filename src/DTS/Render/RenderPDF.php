<?php declare(strict_types=1);


namespace HomeCEU\DTS\Render;


use mikehaertl\wkhtmlto\Pdf;

class RenderPDF extends Render {
  protected const PDF_OPTIONS = [
      'no-outline',
      'exclude-from-outline',
      'margin-bottom' => 0,
      'margin-right'  => 10,
      'margin-left'   => 5,
      'commandOptions' => ['enableXvfb' => true]
  ];

  private Pdf $pdf;

  private function __construct() {
    $this->pdf = new Pdf(self::PDF_OPTIONS);
  }

  public static function create(): RenderInterface {
    return new static();
  }

  public function render(string $compiledTemplate, array $data = []): string {
    $t = $this->renderToString($compiledTemplate, $data);
    $this->pdf->addPage($t);
    return $this->saveToFile();
  }

  public function getContentType(): string {
    return 'application/pdf';
  }

  private function saveToFile(): string {
    $path = $this->createTmpFileName('pdf_');

    if ($this->pdf->saveAs($path))
      return $path;
    throw new RenderException("There was an issue rendering the PDF - {$this->pdf->getError()}");
  }
}
