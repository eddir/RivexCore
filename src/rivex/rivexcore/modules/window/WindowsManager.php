<?php

namespace rivex\rivexcore\modules\window;

/**
 * RivexCore
 *
 * @owner   Rivex™
 * @link    http://rivex.online
 * @link    admin@rivex.online
 *
 * @author  Eduard Rostkov
 * @link    http://rostkov.pro
 * @link    eddirworkmail@gmail.com
 *
 * January 2018
 */

use Ds\Set;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use rivex\rivexcore\Main;

use rivex\rivexcore\modules\window\primal\command\DeletehomeWindow;
use rivex\rivexcore\modules\window\primal\command\fraction\CreateFractionWindow;
use rivex\rivexcore\modules\window\primal\command\fraction\FractionWindow;
use rivex\rivexcore\modules\window\primal\command\fraction\GeneratorFractionWindow;
use rivex\rivexcore\modules\window\primal\command\fraction\GeneratorMenuFractionWindow;
use rivex\rivexcore\modules\window\primal\command\fraction\JoinFractionWindow;
use rivex\rivexcore\modules\window\primal\command\fraction\LeaveFractionWindow;
use rivex\rivexcore\modules\window\primal\command\fraction\MembersFractionWindow;
use rivex\rivexcore\modules\window\primal\command\fraction\RemoveFractionWindow;
use rivex\rivexcore\modules\window\primal\command\fraction\StatusFractionWindow;
use rivex\rivexcore\modules\window\primal\command\fraction\TopFractionWindow;
use rivex\rivexcore\modules\window\primal\command\HelpWindow;
use rivex\rivexcore\modules\window\primal\command\HomesWindow;
use rivex\rivexcore\modules\window\primal\command\ReportWindow;


use rivex\rivexcore\modules\window\primal\AccountWindow;
use rivex\rivexcore\modules\window\primal\command\SethomeWindow;
use rivex\rivexcore\modules\window\primal\ContactWindow;
use rivex\rivexcore\modules\window\primal\LegendWindow;
use rivex\rivexcore\modules\window\primal\CommandsWindow;
use rivex\rivexcore\modules\window\primal\MailboxWindow;
use rivex\rivexcore\modules\window\primal\MailWindow;
use rivex\rivexcore\modules\window\primal\ParcelWindow;
use rivex\rivexcore\modules\window\primal\ServersWindow;
use rivex\rivexcore\utils\exception\LogicException;

class WindowsManager implements Listener
{

    // Комментария на русском, о да. Кодировка, не бей!!(9

    // TODO: это костыль. Некоторые плагины тоже пользуются формами.
    const FIRST_ID = 2000;

    /** @var int */
    private $currentId = self::FIRST_ID;
    /** @var Window[] */
    private $windows = array();
    /** @var Window[] */
    private $pages = array();
    /** @var Main */
    private $main;

    /**
     * @param Main $main
     */
    public function __construct(Main $main)
    {
        $this->main = $main;
        $this->load();
    }

    public function reload()
    {
        $this->currentId = self::FIRST_ID;
        $this->windows = array();
        $this->pages = array();
        $this->load();
    }

    public function load()
    {
        $this->add(HelpWindow::class);
        $this->add(ReportWindow::class);
        $this->add(FractionWindow::class);
        $this->add(CreateFractionWindow::class);
        $this->add(JoinFractionWindow::class);
        $this->add(TopFractionWindow::class);
        $this->add(StatusFractionWindow::class);
        $this->add(RemoveFractionWindow::class);
        $this->add(MembersFractionWindow::class);
        $this->add(LeaveFractionWindow::class);
        $this->add(GeneratorFractionWindow::class);
        $this->add(GeneratorMenuFractionWindow::class);
		$this->add(AccountWindow::class);
		$this->add(ContactWindow::class);
		$this->add(LegendWindow::class);
		$this->add(CommandsWindow::class);
		$this->add(ServersWindow::class);
		$this->add(MailboxWindow::class);
		$this->add(MailWindow::class);
		$this->add(ParcelWindow::class);
		$this->add(HomesWindow::class);
		$this->add(DeletehomeWindow::class);
		$this->add(SethomeWindow::class);
    }

    public function add($window)
    {
        return $this->register(new $window($this->currentId++));
    }

    /**
     * @param Window $window
     * @return Window
     */
    public function register(Window $window)
    {
        if (isset($this->windows[$window->getId()])) {
            throw new LogicException('Window::$id is already used in WindowsManager.');
        }
        if ($window->getName() !== null) {
            $this->pages[$window->getName()] = $window;
        }
        $this->windows[$window->getId()] = $window;
        return $window;
    }

    public function unregister(Window $window)
    {
        if (isset($this->windows[$window->getId()])) {
            unset($this->windows[$window->getId()]);
        }
    }

    /**
     * @param DataPacketReceiveEvent $event
     */
    public function onPacketReceived(DataPacketReceiveEvent $event): void
    {
        $pk = $event->getPacket();
        if ($pk instanceof ModalFormResponsePacket) {
            $player = $event->getPlayer();
            $formId = $pk->formId;
            $data = json_decode($pk->formData, true);
            if (isset($this->windows[$formId])) {
                $window = $this->windows[$formId];
                $response = $window->getUI()->handle($data, $player);//TODO
                if (!is_null($response)) {
                    $callable = $window->getCallable();
                    if (!is_array($data)) {
                        $data = [$data];
                    }
                    if ($callable == null) {
                        $this->windows[$formId]->handle($event->getPlayer(), $response);
                    } else {
                        $callable($event->getPlayer(), $response);
                    }
                    $event->setCancelled();
                }
            }
        }
    }

    /**
     * @return int
     */
    public function getCurrentId()
    {
        return $this->currentId;
    }

    /**
     * @param string $name
     *
     * @return Window|bool
     */
    public function getByName($name)
    {
        return isset($this->pages[$name]) ? $this->pages[$name]->choice() : false;
    }

    /**
     * @param int $id
     *
     * @return Window|bool
     */
    public function get($id)
    {
        return isset($this->windows[$id]) ? $this->windows[$id]->choice() : false;
    }

}
