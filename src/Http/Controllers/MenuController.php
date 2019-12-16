<?php

namespace SertxuDeveloper\Lyra\Http\Controllers;

use Illuminate\Support\Collection;
use SertxuDeveloper\Lyra\Lyra;

class MenuController extends Controller {

  /**
   * Get the menu collection
   * @return Collection
   */
  public function getMenu(): Collection {

    $items = collect(config('lyra.menu'));
    $menu = collect([]);
    $items->filter(function (&$item) use ($menu) {

      if (isset($item['hidden']) && $item['hidden'] === true) return false;

      if (isset($item['items'])) {

        $filtered = collect($item['items'])->filter(function (&$item) {
          if (isset($item['hidden']) && $item['hidden'] === true) return false;
          return $this->can($item);
        });

        $item['items'] = $filtered->toArray();

        if (!$filtered->count()) return false;

        $menu->push($item);
        return true;
      } else {
        $menu->push($item);
        return $this->can($item);
      }
    });
    return $menu;
  }

  /**
   * Check if user can access to the $item
   * @param array $item
   * @return bool
   */
  private function can(array $item): bool {
//    $regex = str_replace('/', '\/', preg_quote(route(config('lyra.routes.web.name') . 'dashboard')));
//    $slug = preg_replace('/' . $regex . '/', '', $this->link($item, true));
//    $slug = str_replace('/', '', $slug);
//
//    $slug = ($slug == "") ? 'lyra' : $slug;

//    return auth()->user()->hasPermission('read_' . $slug);
    if (config('lyra.authenticator') == 'basic') return true;
    return Lyra::auth()->user()->hasPermission('read_' . $item['key']);
  }

  /**
   * Return a relative|absolute url to the current $item menu
   * @param array $item
   * @param bool $absolute
   * @return \Illuminate\Contracts\Routing\ UrlGenerator|string
   */
  public static function link(array $item, $absolute = false): string {
    if (isset($item['url']) && $item['url']) {
      return $absolute ? url(config('lyra.routes.web.prefix') . "/{$item['url']}") : $item['url'];
    } else if (isset($item['route']) && $item['route']) {
      return route($item['route'], null, $absolute);
    }
    return abort('404', "MenuItem {$item['name']} has no url or route defined");
  }

}
