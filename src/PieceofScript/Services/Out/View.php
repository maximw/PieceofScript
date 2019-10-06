<?php


namespace PieceofScript\Services\Out;

use PieceofScript\Services\Errors\InternalError;

/**
 * Class View
 *
 * @package PieceofScript\Services\Out
 */
class View
{
    protected $templateDir;

    public function __construct()
    {
        $this->templateDir = __DIR__ . DIRECTORY_SEPARATOR . 'HtmlTemplates';
    }

    public function render(string $fileName, array $data): string
    {
        $fileName =  $this->templateDir . DIRECTORY_SEPARATOR . $fileName . '.php';

        if (!is_readable($fileName)) {
            throw new InternalError('Template ' . $fileName . ' not found');
        }

        $result = (function($fileName, array $data = []) {
            ob_start();
            extract($data, EXTR_SKIP);
            try {
                include $fileName;
            } catch (\Exception $e) {
                ob_end_clean();
                throw $e;
            }
            return ob_get_clean();
        })($fileName, $data);

        return $result;
    }

}