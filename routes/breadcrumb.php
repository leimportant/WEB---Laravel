<?php

class Breadcrumb {

    public static function render(ContentPage $page)
    {
        $crumbs = self::getCrumbs($page);
        return self::renderCrumbs($crumbs);
    }

    public static function getCrumbs($page)
    {
        $crumbs = [];

        foreach ( $page->getAncestors() as $key => $node )
        {
            $parent = '/';
            if ( $key > 0)
            {
                $parent = $crumbs[$key-1]['url'] . '/';
            }

            $crumbs[] = [
                'title' => $node->crumb,
                'url'   => $parent . self::crumbToUrl($node->crumb),
            ];
        }

        // Current page
        $crumbs[] = [
            'title' => $page->title,
            'url'   => end($crumbs)['url'] . '/' . self::crumbToUrl($page->crumb),
        ];

        return $crumbs;
    }

    private static function crumbToUrl($crumb)
    {
        $title = str_replace( ' ', '-', $crumb);
        return Str::lower($title);
    }

    private static function renderCrumbs($crumbs)
    {
        $currentPage = array_pop($crumbs);

        $html = '<ul class="breadcrumbs">';
        foreach ($crumbs as $crumb)
        {
            $html .= '<li><a href="'.$crumb['url'].'">'.$crumb['title'].'</a></li>';
        }
        $html .= '<li class="active">'.$currentPage['title'].'</li></ul>';

        return $html;
    }