<?php

namespace App\Http\Responder;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Response;

class BaseLayoutViewResponder
{
    /**
     * Response instance.
     *
     * @var \Illuminate\Http\Response
     */
    protected $response;

    /**
     * View Factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * Create a new BaseLayoutViewResponder instance.
     *
     * @param \Illuminate\Http\Response          $response
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function __construct(Response $response, ViewFactory $view)
    {
        $this->response = $response;
        $this->view = $view;
    }

    /**
     * List of links for dropdown menu in page header.
     *
     * @return array
     */
    public function dropdownMenuLinks(): array
    {
        return [
            ['link' => 'v1_top', 'caption' => __('Switch to Previous version')],
            ['link' => 'settings', 'caption' => __('Settings')],
        ];
    }
}
