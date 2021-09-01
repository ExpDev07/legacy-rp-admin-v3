<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Throwable;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     * @return void
     * @throws Exception|Throwable
     */
    public function report(Throwable $exception)
    {
        if (
            $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException || // 404 doesn't need to be logged to avoid filling the log file
            $exception instanceof \Illuminate\Encryption\MissingAppKeyException // /api/players Illuminate\Encryption\MissingAppKeyException: No application encryption key has been specified.
        ) {
            parent::report($exception);
            return;
        }

        $log = storage_path('logs/' . CLUSTER . '_error-' . date('Y-m-d') . '.log');
        $timestamp = date(\DateTimeInterface::RFC3339);
        $trace = $exception->getTrace();
        $stack = [];

        $base = realpath(__DIR__ . '/../../');
        foreach ($trace as $index => $item) {
            $index = (sizeof($trace) - 1) - $index;

            if (!isset($item['file'])) {
                $item['file'] = 'internal';
            }
            if (!isset($item['line'])) {
                $item['line'] = 'internal';
            }

            $item['file'] = str_replace($base, '', $item['file']);

            if (Str::startsWith($item['file'], '/vendor/') || Str::startsWith($item['file'], '\\vendor\\')) {
                if (empty($stack) || $stack[sizeof($stack) - 1] !== '[...]') {
                    $stack[] = '[...]';
                }
                continue;
            }

            $args = !empty($item['args']) ? array_map(function ($arg) {
                $type = gettype($arg);
                switch ($type) {
                    case 'object':
                        $type = get_class($arg);
                        break;
                    case 'array':
                        $type .= '[' . sizeof($arg) . ']';
                        break;
                    case 'string':
                        $type .= '[' . strlen($arg) . ']';
                        break;
                    case 'integer':
                        $type .= '(' . $arg . ')';
                        break;
                    case 'boolean':
                        $type .= '(' . ($arg ? 'true' : 'false') . ')';
                        break;
                }

                return $type;
            }, $item['args']) : [];

            $line = '#' . $index . ' ' . $item['file'] . '(' . $item['line'] . '): ';
            if (isset($item['class']) && isset($item['type'])) {
                $line .= $item['class'] . $item['type'];
            }
            $line .= $item['function'] . '(' . implode(', ', $args) . ')';

            $stack[] = $line;
        }

        $stack = get_class($exception) . ': ' . $exception->getMessage() . PHP_EOL . '        ' . implode(PHP_EOL . '        ', array_reverse($stack));
        $path = explode('?', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '*')[0];

        file_put_contents($log, '[' . $timestamp . '] ' . $path . PHP_EOL .
            '    ' . $stack . PHP_EOL . PHP_EOL, FILE_APPEND);

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

}
