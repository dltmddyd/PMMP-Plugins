<?php

namespace Trampoline;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\block\Block;
use Trampoline\task\fallenTimeOutTask;

class Trampoline extends PluginBase implements Listener {
	public $fallen = [ ];
	public function onEnable() {
		$this->getServer ()->getPluginManager ()->registerEvents ( $this, $this );
	}
	public function onMove(PlayerMoveEvent $event) {
		$player = $event->getPlayer ();
		
		if ($player == null) return;
		if ($player->getLevel () == null) return;
		
		// under
		$x = ( int ) round ( $player->x - 0.5 );
		$y = ( int ) round ( $player->y - 1 );
		$z = ( int ) round ( $player->z - 0.5 );
		
		$id = $player->getLevel ()->getBlockIdAt ( $x, $y, $z );
		$data = $player->getLevel ()->getBlockDataAt ( $x, $y, $z );
		
		if ($id == 35 and $data == 5) {
			$this->fallenQueue ( $player );
			$player->addEntityMotion ( $player->getId (), 0, 3, 0 );
		} else if ($id == 35 and $data == 4) {
			$this->fallenQueue ( $player );
			$player->addEntityMotion ( $player->getId (), 0, 1, 0 );
		} else if ($id == 35 and $data == 10) {
			$this->fallenQueue ( $player );
			$player->addEntityMotion ( $player->getId (), 0, 5, 0 );
		} else if ($id == Block::DIAMOND_BLOCK) {
			$x = - \sin ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI );
			$y = - \sin ( $player->pitch / 180 * M_PI );
			$z =\cos ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI );
			$this->fallenQueue ( $player );
			$player->addEntityMotion ( $player->getId (), $x * 3, $y * 3, $z * 3 );
		} else {
			
			// right
			$x = ( int ) round ( $player->x - 1.5 );
			$y = ( int ) round ( $player->y );
			$z = ( int ) round ( $player->z - 0.5 );
			
			$id = $player->getLevel ()->getBlockIdAt ( $x, $y, $z );
			$data = $player->getLevel ()->getBlockDataAt ( $x, $y, $z );
			
			if ($id == 35 and $data == 5) {
				$this->fallenQueue ( $player );
				$player->addEntityMotion ( $player->getId (), + 3, 0, 0 );
			} else if ($id == 35 and $data == 4) {
				$this->fallenQueue ( $player );
				$player->addEntityMotion ( $player->getId (), + 1, 0, 0 );
			} else if ($id == 35 and $data == 10) {
				$this->fallenQueue ( $player );
				$player->addEntityMotion ( $player->getId (), + 5, 0, 0 );
			} else {
				// left
				$x = ( int ) round ( $player->x + 0.5 );
				$y = ( int ) round ( $player->y );
				$z = ( int ) round ( $player->z - 0.5 );
				
				$id = $player->getLevel ()->getBlockIdAt ( $x, $y, $z );
				$data = $player->getLevel ()->getBlockDataAt ( $x, $y, $z );
				
				if ($id == 35 and $data == 5) {
					$this->fallenQueue ( $player );
					$player->addEntityMotion ( $player->getId (), - 3, 0, 0 );
				} else if ($id == 35 and $data == 4) {
					$this->fallenQueue ( $player );
					$player->addEntityMotion ( $player->getId (), - 1, 0, 0 );
				} else if ($id == 35 and $data == 10) {
					$this->fallenQueue ( $player );
					$player->addEntityMotion ( $player->getId (), - 5, 0, 0 );
				} else {
					// north
					$x = ( int ) round ( $player->x - 0.5 );
					$y = ( int ) round ( $player->y );
					$z = ( int ) round ( $player->z - 1.5 );
					
					$id = $player->getLevel ()->getBlockIdAt ( $x, $y, $z );
					$data = $player->getLevel ()->getBlockDataAt ( $x, $y, $z );
					
					if ($id == 35 and $data == 5) {
						$this->fallenQueue ( $player );
						$player->addEntityMotion ( $player->getId (), 0, 0, + 3 );
					} else if ($id == 35 and $data == 4) {
						$this->fallenQueue ( $player );
						$player->addEntityMotion ( $player->getId (), 0, 0, + 1 );
					} else if ($id == 35 and $data == 10) {
						$this->fallenQueue ( $player );
						$player->addEntityMotion ( $player->getId (), 0, 0, + 5 );
					} else {
						// north
						$x = ( int ) round ( $player->x - 0.5 );
						$y = ( int ) round ( $player->y );
						$z = ( int ) round ( $player->z + 0.5 );
						
						$id = $player->getLevel ()->getBlockIdAt ( $x, $y, $z );
						$data = $player->getLevel ()->getBlockDataAt ( $x, $y, $z );
						
						if ($id == 35 and $data == 5) {
							$this->fallenQueue ( $player );
							$player->addEntityMotion ( $player->getId (), 0, 0, - 3 );
						} else if ($id == 35 and $data == 4) {
							$this->fallenQueue ( $player );
							$player->addEntityMotion ( $player->getId (), 0, 0, - 1 );
						} else if ($id == 35 and $data == 10) {
							$this->fallenQueue ( $player );
							$player->addEntityMotion ( $player->getId (), 0, 0, - 5 );
						}
					}
				}
			}
		}
	}
	public function preventFlyKick(PlayerKickEvent $event) {
		if (isset ( $this->fallen [$event->getPlayer ()->getName ()] )) {
			if ($event->getReason () == "Flying is not enabled on this server") $event->setCancelled ();
		}
	}
	public function fallenQueue(Player $player) {
		if ($player == null) return;
		if (isset ( $this->fallen [$player->getName ()] )) {
			$this->fallen [$player->getName ()] ++;
		} else {
			$this->fallen [$player->getName ()] = 1;
		}
		$this->getServer ()->getScheduler ()->scheduleDelayedTask ( new fallenTimeOutTask ( $this, $player->getName () ), 100 );
	}
	public function fallenTimeOut($name) {
		if (isset ( $this->fallen [$name] )) $this->fallen [$name] --;
	}
	public function fallenDamagePrevent(EntityDamageEvent $event) {
		if ($event->getCause () == EntityDamageEvent::CAUSE_FALL) {
			if (! $event->getEntity () instanceof Player) return;
			$event->setCancelled ();
			return;
			if (isset ( $this->fallen [$event->getEntity ()->getName ()] )) {
				$event->setCancelled ();
				$this->fallen [$event->getEntity ()->getName ()] --;
				if ($this->fallen [$event->getEntity ()->getName ()] > 1) unset ( $this->fallen [$event->getEntity ()->getName ()] );
			}
		}
	}
}

?>