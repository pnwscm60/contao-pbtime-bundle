<?php
/*
 * This file is part of Pbtime.
 *
 * (c) Markus Schenker
 *
 * @license LGPL-3.0-or-later
 */
namespace Pnwscm60\ContaoPbtimeBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Pnwscm60\ContaoPbtimeBundle\Pnwscm60ContaoPbtimeBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ContaoPbtimeBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
