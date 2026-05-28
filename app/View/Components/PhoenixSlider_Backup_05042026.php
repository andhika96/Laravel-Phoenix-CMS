<?php

namespace App\View\Components;

use Closure;
use Browser;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

use App\Models\CoverImage\CoverImage;

class PhoenixSlider extends Component
{
	/**
	 * Create a new component instance.
	 */
	public function __construct($uri, $type)
	{
		$this->uri = $uri;
		$this->type = $type;
	}

	/**
	 * Get the view / contents that represent the component.
	 */
	public function render(): View|Closure|string
	{
		$getCoverImageDetail = CoverImage::where('uri', $this->uri)->first();

		// dd($getCoverImageDetail['cover_slideshow_mobile_direction']);

		if ($getCoverImageDetail)
		{
			if (Browser::isDesktop() || Browser::isTablet())
			{
				if ($getCoverImageDetail['cover_type'] == 'background_image' ||
					$getCoverImageDetail['cover_type'] == 'slideshow' &&
					$getCoverImageDetail['cover_slideshow_desktop_direction'] == 'horizontal')
				{
					return view('components.slider.desktop.phoenix-slider-horizontal-desktop', ['data' => $getCoverImageDetail]);
				}
				elseif ($getCoverImageDetail['cover_type'] == 'slideshow' &&
						$getCoverImageDetail['cover_slideshow_desktop_direction'] == 'vertical')
				{
					return view('components.slider.desktop.phoenix-slider-vertical-desktop', ['data' => $getCoverImageDetail]);
				}
				else
				{
					return view('components.slider.mobile.phoenix-slider-horizontal-mobile', ['data' => $getCoverImageDetail]);
				}
			}
			elseif (Browser::isMobile())
			{
				if ($getCoverImageDetail['cover_type'] == 'background_image' ||
					$getCoverImageDetail['cover_type'] == 'slideshow' &&
					$getCoverImageDetail['cover_slideshow_mobile_direction'] == 'horizontal')
				{
					return view('components.slider.mobile.phoenix-slider-horizontal-mobile', ['data' => $getCoverImageDetail]);
				}
				elseif ($getCoverImageDetail['cover_type'] == 'slideshow' &&
						$getCoverImageDetail['cover_slideshow_mobile_direction'] == 'vertical')
				{
					return view('components.slider.mobile.phoenix-slider-vertical-mobile', ['data' => $getCoverImageDetail]);
				}
				else
				{
					return view('components.slider.mobile.phoenix-slider-horizontal-mobile', ['data' => $getCoverImageDetail]);
				}
			}
		}
	}

	/*
	public function render(): View|Closure|string
	{
		$getCoverImageDetail = CoverImage::where('uri', $this->uri)->first();

		// dd($getCoverImageDetail['cover_slideshow_mobile_direction']);

		if ($getCoverImageDetail)
		{
			if ($getCoverImageDetail['cover_slideshow_desktop_direction'] == 'horizontal' ||
				$getCoverImageDetail['cover_slideshow_mobile_direction'] == 'horizontal')
			{
				if (Browser::isDesktop() || Browser::isTablet())
				{
					return view('components.slider.desktop.phoenix-slider-horizontal-desktop', ['data' => $getCoverImageDetail]);
				}
				elseif (Browser::isMobile())
				{
					return view('components.slider.mobile.phoenix-slider-horizontal-mobile', ['data' => $getCoverImageDetail]);
				}
			}
			elseif ($getCoverImageDetail['cover_slideshow_desktop_direction'] == 'vertical' ||
					$getCoverImageDetail['cover_slideshow_mobile_direction'] == 'vertical')
			{
				if (Browser::isDesktop() || Browser::isTablet())
				{
					return view('components.slider.desktop.phoenix-slider-vertical-desktop', ['data' => $getCoverImageDetail]);
				}
				elseif (Browser::isMobile())
				{
					return view('components.slider.mobile.phoenix-slider-vertical-mobile', ['data' => $getCoverImageDetail]);
				}
			}
			else
			{
				if (Browser::isDesktop() || Browser::isTablet())
				{
					return view('components.slider.desktop.phoenix-slider-horizontal-desktop', ['data' => $getCoverImageDetail]);
				}
				elseif (Browser::isMobile())
				{
					return view('components.slider.mobile.phoenix-slider-horizontal-mobile', ['data' => $getCoverImageDetail]);
				}
			}
		}
	}
	*/

	/*
	public function render(): View|Closure|string
	{
		$getCoverImageDetail = CoverImage::where('uri', $this->uri)->first();

		if ($this->type == 'horizontal')
		{
			if (Browser::isDesktop() || Browser::isTablet())
			{
				return view('components.slider.desktop.phoenix-slider-horizontal-desktop');
			}
			elseif (Browser::isMobile())
			{
				return view('components.slider.mobile.phoenix-slider-horizontal-mobile');
			}
		}
		elseif ($this->type == 'vertical')
		{
			if (Browser::isDesktop() || Browser::isTablet())
			{
				return view('components.slider.desktop.phoenix-slider-vertical-desktop');
			}
			elseif (Browser::isMobile())
			{
				return view('components.slider.mobile.phoenix-slider-vertical-mobile');
			}
		}
		else
		{
			if (Browser::isDesktop() || Browser::isTablet())
			{
				return view('components.slider.desktop.phoenix-slider-horizontal-desktop');
			}
			elseif (Browser::isMobile())
			{
				return view('components.slider.mobile.phoenix-slider-horizontal-mobile');
			}
		}
	}
	*/
}
