<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Collection;
use Livewire\Component;

class InfinitePostListing extends Component
{
    // public $posts;

    // public $nextCursor;

    // public $hasMorePages;

    // public function mount()
    // {
    //     $this->posts = new Collection();

    //     $this->loadPosts();
    // }

    // public function loadPosts()
    // {
    //     if ($this->hasMorePages !== null  && ! $this->hasMorePages) {
    //         return;
    //     }

    //     $posts = Post::cursorPaginate(12, ['*'], 'cursor', Cursor::fromEncoded($this->nextCursor));

    //     $this->posts->push(...$posts->items());

    //     if ($this->hasMorePages = $posts->hasMorePages()) {
    //         $this->nextCursor = $posts->nextCursor()->encode();
    //     }
    // }

    // public function render()
    // {
    //     return view('livewire.infinite-post-listing')->layout('layouts.base');
    // }
        foreach ($pupils as $pupil){

            foreach($subjectAverages as $sub_id => $sub_averages){

                $p_sub_av = $sub_averages[$pupil->id];

                if($p_sub_av !== null){

                    $pupil_average_sum = $pupil_average_sum + ($p_sub_av * $classeCoefTabs[$sub_id]);

                    $coef_total = $coef_total + $classeCoefTabs[$sub_id];

                }
                
            }


            if($pupil_average_sum && $coef_total > 0){

                $pupil_average = floatval(number_format(($pupil_average_sum /($coef_total)), 2));

            }
            else{

                $pupil_average = null;


            }


            $semestrialAverage[$pupil->id] = $pupil_average;

        }






}