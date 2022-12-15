<?php
$post->username = !empty($post->username) ? $post->username : @$post->user->name;
$json = ['id' => $post->id, 'image' => $post->main_images->first()->url ?? '', // full URL of image
    'category' => $post->category_class,                                       // category name lower case
    'username' => $post->username,                                             // username
    'tags' => $post->tags->map(function ($tag) {
        return '#' . $tag->name;
    })->toArray(),                                // tags
    'url' => route('web.detail', $post->uuid),    // full URL of detail page
    'date' => $post->local_time->format(__('pages/toppage.date')), // date
    'photo' => $post->user && $post->user->photo ? json_decode($post->user->photo)->url : '',
    'youtube_url' => $post->youtube ?: '',
    'note' => !empty($post->note_translated) ? handle_note_post($post->note_translated) : '...'
];
?>



<!-- Static text -->
@php
    $COMMENT_DISABLED = __('pages/toppage.cannot');
    $NO_COMMENT_TEXT = __('pages/toppage.no_comment_yet');
@endphp

<!-- App layout -->
@extends('web.layouts.app')

<?php
$meta_title = $post->username . 'のPCデスク周り - DIGITAL DIYer';
$meta_description = $post->username . ' のPCデスク周りの詳細ページです。PCデスク周りの画像やPCスペック、こだわりのポイントを確認できます。 #お前らのpcデスク周り晒していけ';
?>

<!-- Add title tag -->
@section('pageTitle', $meta_title)

<!-- Meta -->
@push('meta')
    <meta content="{{$meta_description}}" name="description">
    <meta property="og:title" content="{{$meta_title}}"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="{{route('web.detail', $post->uuid)}}"/>
    <meta property="og:image" content="{{$post->main_images->first()->url ?? ''}}"/>
    <meta property="og:site_name" content="Digital Diyer - ゲーム部屋・PCデスク周りを見たい/投稿したい人向けの晒しサイト"/>
    <meta property="og:description" content="{{$meta_description}}"/>
    <meta name="twitter:description" content="{{$meta_description}}"/>
    <meta name="twitter:title" content="{{$meta_title}}"/>
    <meta name="twitter:image" content="{{$post->main_images->first()->url ?? ''}}"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

<!-- Main class -->
@section('mainClass', 'p-detail')

<!-- Main page -->
@section('main')
    <!-- Title -->
    <section class="p-detail-main js-observation-target">
        <div class="section-inner inner">
            <div class="content">
                <div class="pc">
                    <!-- Category -->
                    <div class="content-heading">
                        <div class="detail-category detail-category--{{$post->category_class}}">
                            {{$post->category_name}}
                        </div>
                        <div class="detail-date">
                            {{$post->local_time->format(__('pages/toppage.date'))}}
                        </div>
                    </div>

                    <!-- Link, Username -->
                    <div class="content-title">
                        <div class="detail-title">
                            <div>
                                @if(!empty($post->user->photo))
                                    <img class="detail-title__img" src="{{ json_decode($post->user->photo)->url }}"
                                         alt="">
                                @else
                                    <img class="detail-title__img-non-obj"
                                         src="{{ asset('assets/images/common/no-photo.png') }}"
                                         alt="">
                                @endif
                            </div>
                            <div>
                                <h1>
                                    <a class="detail-title-link {{ empty($post->user_id) ? 'detail-title-link-default' : ''}}"
                                       href="{{ !empty($post->user_id) ? route('web.account.overview', ['id' => $post->user_id]) : 'javascript:;' }}">{{$post->username}}</a>
                                </h1>
                                @if($post->twitter)
                                    <a class="detail-user-link" href="https://twitter.com/{{$post->twitter}}"
                                       target="_blank">
                                        {{ "@" . str_replace('@','',$post->twitter) }}
                                        <span class="detail-user-ico m-ico-link">
                                            <x-web.svg svg="ico-link"></x-web.svg>
                                        </span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="detail-follow">
                            <a class="detail-user-link" href="{{ !empty($post->user_id) ? route('web.account.following', ['id' => $post->user_id]) : 'javascript:;' }}">
                                <p>{{ __('pages/common.follow') }} <span
                                            class="text-neon"> {{ count(optional($post->user)->following() ?? []) }}</span>
                                </p>
                            </a>
                            <a class="detail-user-link" href="{{ !empty($post->user_id) ? route('web.account.followers', ['id' => $post->user_id]) : 'javascript:;' }}">
                                <p class="detail-follow-margin">{{ __('pages/common.follower') }} <span
                                            class="text-neon"> {{ count(optional($post->user)->follower() ?? []) }}</span>
                                </p>
                            </a>
                        </div>
                    </div>

                    <!-- Action like, follow -->
                    <div class="detail-user fix-share-responsive">
                        @if(!empty($post->user->instagram) || !empty($post->user->twitter) || !empty($post->user->youtube) )
                            <div class="social-list">
                                <ul class="sns-lists">
                                    @if(!empty($post->user->instagram))
                                        <li class="sns-list">
                                            <a href="{{ $post->user->instagram }}" class="sns-link" target="_blank">
                                                <div class="icon-item">
                                                    <i class="fa fa-instagram"></i>
                                                </div>
                                            </a>
                                        </li>
                                    @endif
                                    @if(!empty($post->user->twitter))
                                        <li class="sns-list">
                                            <a href="{{ strpos($post->user->twitter,'twitter.com') ?  $post->user->twitter : 'https://twitter.com/'.$post->user->twitter }}"
                                               class="sns-link" target="_blank">
                                                <div class="icon-item">
                                                    <i class="fa fa-twitter"></i>
                                                </div>
                                            </a>
                                        </li>
                                    @endif
                                    @if(!empty($post->user->youtube))
                                        <li class="sns-list">
                                            <a href="{{ $post->user->youtube }}" class="sns-link" target="_blank">
                                                <div class="icon-item">
                                                    <i class="fa fa-youtube-play"></i>
                                                </div>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                        <div class="d-flex content-like-box {{ !empty(Auth::user()->id) ?? 'justify-content-around' }}">
                            <!-- If Member else Guest -->
                            @if(!empty(Auth::user()->id))
                                <div class="like-box {{ Auth::user()->hasLiked($post) ? 'active-like' : '' }}">
                                      <span class="ico-like">
                                        <x-web.svgg svg="ico-like"></x-web.svgg>
                                      </span>
                                    <div class="fs-14 count-like text-neon">{{ $post->likers($post->id)->count() }}</div>
                                </div>
                            @else
                                <div class="like-box {{ \App\Entity\Tracker::tracker()->hasLikedByGuest($post) ? 'active-like' : ''}}">
                                    <span class="ico-like">
                                      <x-web.svgg svg="ico-like"></x-web.svgg>
                                    </span>
                                    <div class="fs-14 count-like text-neon">{{ $post->likers($post->id)->count() }}</div>
                                </div>
                            @endif

                        <!-- Follow button -->
                            @if(!empty(Auth::user()->id) && $post->user_id != 0 && $post->user_id != Auth::user()->id)
                                <div class="follow-button {{ in_array($post->user_id,Auth::user()->following()) ? 'active' : '' }}">
                                    <a class="fs-12 text-neon" href="{{ route('web.account.follow',$post->user_id) }}">
                                        {{ in_array($post->user_id, Auth::user()->following()) ? __('pages/followingfollower.following') : __('pages/followingfollower.follow') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{--The number of likers PC--}}
                    @if($post->likers($post->id)->count() > 0)
                        <div id="user-liked-list" class="user-liked-list" onclick="HandleClickUserListPc()">
                            @for($i = 0; $i < count(array_slice($images, 0, 3)); $i++)
                                <img src="{{ $images[$i] }}" class="avatar avatar-{{$i}}" alt="avatar">
                            @endfor
                            @if(count($images) >= 3)
                                <span class="count-like count-like-3 text-neon">{{ $post->likers($post->id)->count() }}</span>
                            @elseif (count($images) == 2)
                                <span class="count-like count-like-2 text-neon">{{ $post->likers($post->id)->count() }}</span>
                            @else
                                <span class="count-like count-like-1 text-neon">{{ $post->likers($post->id)->count() }}</span>
                            @endif
                            <span class="user-liked-list-desc">{{ __('pages/common.number_like') }}</span>
                        </div>
                @endif

                {{-- The like list PC  --}}
                @include('web.like-list-pc')

                <!-- Tags -->
                    <div class="detail-block">
                        <h3 class="detail-block-title">TAGS</h3>
                        <div class="detail-block-content">
                            <ul class="detail-tags">
                                @foreach($post->tags->take(11) as $tag)
                                    <li class="detail-tag">
                                        <a class="detail-tag-link"
                                           href="{{ route('web.search.ranking.keyword.root', ['keyword' => $tag->name]) }}">
                                            {{ $tag->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Point -->
                    <div class="detail-block detail-block--point">
                        <h3 class="detail-block-title">POINT</h3>
                        <div class="detail-block-content detail-point">
                            {!! nl2br($post->note_translated) !!}
                        </div>
                    </div>
                </div>

                <!-- Link, Username, Category -->
                <div class="mobile">
                    <div class="content-heading">
                        <div class="detail-category detail-category--{{$post->category_class}}">
                            {{$post->category_name}}
                        </div>
                        <div class="follow-count">
                            <a class="detail-user-link" href="{{ !empty($post->user_id) ? route('web.account.following', ['id' => $post->user_id]) : 'javascript:;' }}">
                                <p>{{ __('pages/common.follow') }} <span
                                            class="text-neon"> {{ count(optional($post->user)->following() ?? []) }}</span>
                                </p>
                            </a>
                            <a class="detail-user-link" href="{{ !empty($post->user_id) ? route('web.account.followers', ['id' => $post->user_id]) : 'javascript:;' }}">
                                <p class="detail-follow-margin">{{ __('pages/common.follower') }} <span
                                            class="text-neon"> {{ count(optional($post->user)->follower() ?? []) }}</span>
                                </p>
                            </a>
                        </div>
                    </div>

                    <div class=" content-title">
                        <div class="detail-title">
                            <div>
                                @if(!empty($post->user->photo))
                                    <img class="detail-title__img" src="{{ json_decode($post->user->photo)->url }}"
                                         alt="">
                                @else
                                    <img class="detail-title__img-non-obj"
                                         src="{{asset('assets/images/common/no-photo.png')}}"
                                         alt="">
                                @endif
                            </div>
                            <div>
                                <h1>
                                    <a class="detail-title-link {{ empty($post->user_id) ? 'detail-title-link-default' : ''}}"
                                       href="{{ !empty($post->user_id) ? route('web.account.overview', ['id' => $post->user_id]) : 'javascript:;' }}">{{$post->username}}</a>
                                </h1>
                                @if($post->twitter)
                                    <a class="detail-user-link" href="https://twitter.com/{{$post->twitter}}"
                                       target="_blank">
                                        {{ "@" . str_replace('@','',$post->twitter) }}
                                        <span class="detail-user-ico m-ico-link">
                                        <x-web.svg svg="ico-link"></x-web.svg>
                                    </span>
                                    </a>
                                @endif
                            </div>
                            <div class="detail-follow">

                                <!-- Follow button -->
                                @if(!empty(Auth::user()->id) && $post->user_id != 0 && $post->user_id != Auth::user()->id)
                                    <div class="follow-button {{ in_array($post->user_id,Auth::user()->following()) ? 'active' : '' }}">
                                        <a class="fs-12 text-neon"
                                           href="{{ route('web.account.follow',$post->user_id) }}">
                                            {{ in_array($post->user_id, Auth::user()->following()) ? __('pages/followingfollower.following') : __('pages/followingfollower.follow') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slider -->
            <div class="slider" style="overflow: hidden">
                <div id="js-slider" class="slider-slide swiper-container">
                    <div class="swiper-wrapper">
                        @if(@$post->youtube)
                            <div class="swiper-slide youtube-slide-item">
                                <x-web.youtube.youtube-embed
                                        :url="@$post->youtube"
                                        width="750"
                                        height="425">
                                </x-web.youtube.youtube-embed>
                            </div>
                        @endif

                        @foreach($post->computer_images as $key => $image)
                            <div class="swiper-slide @if($key === 0) main-image-slide-item @endif">
                                <x-web.lazy-img
                                        :src="$image->url"
                                        :alt="$post->username"
                                        width="750"
                                        height="425">
                                </x-web.lazy-img>
                            </div>
                        @endforeach
                    </div>

                    @if($post->computer_images->count() + $post->sub_images->count() + ($post->youtube ? 1 : 0)  > 1)
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    @endif
                </div>
                <div id="js-thumbs" class="slider-thumbs swiper-container">
                    <div class="swiper-wrapper">
                        @if(@$post->youtube)
                            <div class="swiper-slide youtube-thumb-item">
                                <x-web.lazy-img
                                        :src="youtube_thumbnail(@$post->youtube)"
                                        :alt="@$post->youtube"
                                        width="70"
                                        height="40">
                                </x-web.lazy-img>
                                <x-web.youtube.youtube-icon
                                        class="detail-thumbnail-icon-youtube">
                                </x-web.youtube.youtube-icon>
                            </div>
                        @endif

                        @foreach($post->computer_images as $key => $image)
                            <div class="swiper-slide @if($key === 0) main-image-thumb-item @endif">
                                <x-web.lazy-img
                                        :src="$image->url"
                                        :alt="$post->username"
                                        width="70"
                                        height="40">
                                </x-web.lazy-img>
                            </div>
                        @endforeach
                    </div>
                    <div class="slider-number">
                        <div id="js-slider-current" class="slider-number-current"></div>
                        <div class="slider-progress">
                            <div id="js-slider-progress" class="slider-progress-current"></div>
                        </div>
                        <div id="js-slider-length" class="slider-number-length"></div>
                    </div>
                </div>
            </div>

            <div class="mobile w-100">
                <!-- Action like, follow -->
                <div class="detail-user fix-share-responsive">
                    <div class="d-flex content-like-box justify-content-between">
                        <!-- If Member else Guest -->
                        @if(!empty(Auth::user()->id))
                            <div class="like-box {{ Auth::user()->hasLiked($post) ? 'active-like' : '' }}">
                                <span class="ico-like">
                                      <x-web.svgg svg="ico-like"></x-web.svgg>
                                </span>
                                <div class="fs-14 count-like text-neon">{{ $post->likers($post->id)->count() }}</div>
                            </div>
                        @else
                            <div class="like-box {{ \App\Entity\Tracker::tracker()->hasLikedByGuest($post) ? 'active-like' : ''}}">
                                <span class="ico-like">
                                      <x-web.svgg svg="ico-like"></x-web.svgg>
                                </span>
                                <div class="fs-14 count-like text-neon">{{ $post->likers($post->id)->count() }}</div>
                            </div>
                    @endif

                    <!-- Post date -->
                        <div class="detail-date">
                            {{$post->local_time->format(__('pages/toppage.date'))}}
                        </div>
                    </div>
                    {{--SP--}}
                    @if($post->likers($post->id)->count() > 0)
                        <div id="user-liked-list-sp" class="d-flex user-liked-list" onclick="HandleClickUserListSp()">
                            @for($i = 0; $i < count(array_slice($images, 0, 3)); $i++)
                                <img src="{{ $images[$i] }}" class="avatar avatar-{{$i}}" alt="avatar">
                            @endfor
                            @if(count($images) >= 3)
                                <span class="count-like count-like-3 text-neon">{{ $post->likers($post->id)->count() }}</span>
                            @elseif (count($images) == 2)
                                <span class="count-like count-like-2 text-neon">{{ $post->likers($post->id)->count() }}</span>
                            @else
                                <span class="count-like count-like-1 text-neon">{{ $post->likers($post->id)->count() }}</span>
                            @endif
                            <span class="user-liked-list-desc">{{ __('pages/common.number_like') }}</span>
                        </div>
                    @endif

                    {{-- The like list --}}
                    @include('web.like-list-sp')

                    @if(!empty($post->user->instagram) || !empty($post->user->twitter) || !empty($post->user->youtube) )
                        <div class="social-list">
                            <ul class="sns-lists">
                                @if(!empty($post->user->instagram))
                                    <li class="sns-list">
                                        <a href="{{ $post->user->instagram }}" class="sns-link" target="_blank">
                                            <div class="icon-item">
                                                <i class="fa fa-instagram"></i>
                                            </div>
                                        </a>
                                    </li>
                                @endif
                                @if(!empty($post->user->twitter))
                                    <li class="sns-list">
                                        <a href="{{ strpos($post->user->twitter,'twitter.com') ?  $post->user->twitter : 'https://twitter.com/'.$post->user->twitter }}"
                                           class="sns-link" target="_blank">
                                            <div class="icon-item">
                                                <i class="fa fa-twitter"></i>
                                            </div>
                                        </a>
                                    </li>
                                @endif
                                @if(!empty($post->user->youtube))
                                    <li class="sns-list">
                                        <a href="{{ $post->user->youtube }}" class="sns-link" target="_blank">
                                            <div class="icon-item">
                                                <i class="fa fa-youtube-play"></i>
                                            </div>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Tags, Point -->
                <div class="content border-top-0">
                    <!-- Tags -->
                    <div class="detail-block">
                        <h3 class="detail-block-title">TAGS</h3>
                        <div class="detail-block-content">
                            <ul class="detail-tags">
                                @foreach($post->tags as $tag)
                                    <li class="detail-tag">
                                        <a class="detail-tag-link"
                                           href="{{ route('web.search.ranking.keyword.root', ['keyword' => $tag->name]) }}">
                                            {{$tag->name}}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Point -->
                    <div class="detail-block detail-block--point">
                        <h3 class="detail-block-title">POINT</h3>
                        <div class="detail-block-content detail-point">
                            {!! $post->note_translated !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Content -->
    <section class="p-detail-content">
        <div class="section-inner inner">
            <div class="body fix-spec-responsive">
                <div class="spec-list">
                    <div class="spec">
                        <div class="detail-block detail-block--bottom">
                            <h3 class="detail-block-title">SPEC</h3>
                            <div class="detail-block-content">
                                <ul class="spec-items">
                                    @foreach(config('settings.computer.component.labels', []) as $key => $value)
                                        <li class="spec-item">
                                            <div class="spec-item-key">{{__("pages/newmember.{$key}")}}</div>
                                            <div class="spec-item-text">
                                                @if(!empty(@$post->{$key.'_translated'}))
                                                    <a href="{{ route('web.search.ranking.keyword.root', ['keyword' => !empty(@$post->{$key}) ? @$post->{$key} : '-' ]) }}">
                                                        {{!empty(@$post->{$key.'_translated'}) ? @$post->{$key.'_translated'} : '-'}}
                                                    </a>
                                                @else
                                                    <span>
                                                        {{!empty(@$post->{$key.'_translated'}) ? @$post->{$key.'_translated'} : '-'}}
                                                    </span>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="spec">
                        <div class="detail-block detail-block--bottom">
                            <h3 class="detail-block-title"></h3>
                            <div class="detail-block-content">
                                <ul class="spec-items">
                                    @foreach(config('settings.computer.component.labels2', []) as $key => $value)
                                        <li class="spec-item">
                                            <div class="spec-item-key"> {{  __("pages/newmember.{$key}") }} </div>
                                            <div class="spec-item-text">
                                                @if(!empty(@$post->{$key.'_translated'}))
                                                    <a href="{{ route('web.search.ranking.keyword.root', ['keyword' => @$post->{$key}]) }}">
                                                        {{!empty(@$post->{$key.'_translated'}) ? @$post->{$key.'_translated'} : '-'}}
                                                    </a>
                                                @else
                                                    <span>
                                                        {{!empty(@$post->{$key.'_translated'}) ? @$post->{$key.'_translated'} : '-'}}
                                                    </span>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="memo fix-memo-responsive">
                    <div class="detail-block detail-block--bottom">
                        <h3 class="detail-block-title">MEMO</h3>
                        <div class="detail-block-content">
                            <div class="memo-text memo-item">
                                {!! nl2br($post->other_translated) !!}
                            </div>
                            <div class="memo-sns memo-item">
                                <p class="sns-text">SHARE TO ：</p>
                                <a class="sns-link sns-link-facebook"
                                   href="https://www.facebook.com/sharer/sharer.php?u={{urlencode(route('web.detail', $post->uuid))}}"
                                   target="_blank" rel="nofollow noopener noreferrer">
                                    <span class="sns-ico m-ico-facebook">
                                      <x-web.svg svg="ico-facebook"></x-web.svg>
                                    </span>
                                </a>
                                {{--hashtags={ハッシュタグ}&via={Twitterユーザー名}&related={追加のTwitterユーザー名}&in-reply-to={親ツイートのID}--}}
                                <a class="sns-link sns-link-twitter"
                                   href="https://twitter.com/intent/tweet?text={{urlencode($post->username . 'のPCデスク周り')}}&url={{urlencode(route('web.detail', $post->uuid))}}&hashtags=お前らのpcデスク周り晒していけ,PCSETUP"
                                   target="_blank" rel="nofollow noopener noreferrer">
                                    <span class="sns-ico m-ico-twitter">
                                      <x-web.svg svg="ico-twitter"></x-web.svg>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(!$post->enable_comment)
                <div class="comment-box">
                    <h3 class="caption text-center fs-14">
                        {{ $COMMENT_DISABLED }}
                    </h3>
                </div>
            @else
                <div class="comment-box">
                    <h3 class="caption">{{ __("pages/newmember.comment_1")}} </h3>
                    @if(!empty(Auth::user()->id))
                        <div class="new-comment">
                            <div class="avatar-your">
                                @if(Auth::user()->photo)
                                    <img src="{{ json_decode(Auth::user()->photo)->url }}" alt=""/>
                                @else
                                    <img src="{{asset('assets/images/common/no-photo.png')}}" alt=""
                                         class="ava-default">
                                @endif
                            </div>
                            <form id="comment-form" method="POST" action="javascript:" class="textarea-comment">
                                @csrf
                                <textarea
                                        name="comment"
                                        class="comment-your"
                                        placeholder="{{__('pages/newmember.comment_2')}}"
                                        maxlength="140"
                                        rows="1"
                                ></textarea>
                                <button type="submit" class="btn-comment">{{ __("pages/newmember.send") }}</button>
                            </form>
                        </div>
                    @endif
                    <div class="list-cmt">
                        @if(count($post->comments) === 0)
                            <div class="my-30-px mx-auto">
                                <h3 class="caption text-center fs-14">
                                    {{ $NO_COMMENT_TEXT }}
                                </h3>
                            </div>
                        @else
                            @include('web.shared.post.partials.replies', ['comments' => $post->limitComments])
                        @endif
                    </div>
                    @if(count($post->comments) > 2)
                        <button class="btn-see-more"> {{ __("pages/newmember.see_more") }} </button>
                    @endif
                </div>
            @endif
            <div class="btn">
                <div class="btn-wrap">
                    <a href="{{route('web.form',0)}}" class="m-btn">
                        <span class="btn-camera">
                            <x-web.svg svg="ico-about-post"></x-web.svg>
                        </span>
                        <span class="btn-camera btn-camera--hover">
                            <x-web.svg svg="ico-submit"></x-web.svg>
                        </span>
                        <span class="m-btn-text">{{ __("pages/newmember.your_setup") }}</span>
                        <span class="btn-circle">
                            <x-web.circle-r></x-web.circle-r>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- BTO SECTION --}}
    @if($post->category_name)
        @include('components.web.bto.slider_'.strtolower($post->category_name), ['category' => strtolower($post->category_name)]);
        @push('styles')
            <link rel="stylesheet" href="{{ url('assets/css/page/detail/slider.css') }}">
            <link rel="stylesheet" href="{{ url('assets/css/page/detail/slider-mobile.css') }}">
        @endpush
    @endif
    {{--    @if($post->category_name  === "GAME")--}}
    {{--        @include('components.web.bto.slider_'.strtolower($post->category_name), ['category' => strtolower($post->category_name)]);--}}
    {{--        @push('styles')--}}
    {{--                <link rel="stylesheet" href="{{ url('assets/css/page/detail/slider.css') }}">--}}
    {{--                <link rel="stylesheet" href="{{ url('assets/css/page/detail/slider-mobile.css') }}">--}}
    {{--        @endpush--}}
    {{--    @elseif ($post->category_name  === "CREATIVE")    --}}
    {{--        @include('components.web.bto.recommend_creative')--}}
    {{--        @push('styles')--}}
    {{--            <link rel="stylesheet" href="{{ url('assets/css/page/detail/recommend.css') }}">--}}
    {{--        @endpush--}}
    {{--    @else--}}
    {{--        @include('components.web.bto.recommend')--}}
    {{--        @push('styles')--}}
    {{--            <link rel="stylesheet" href="{{ url('assets/css/page/detail/recommend.css') }}">--}}
    {{--        @endpush--}}
    {{--    @endif--}}

    <!-- タグが同じ記事 -->
    <section class="p-detail-tags js-observation-target">
        <div class="section-inner">
            <h2 class="m-section-title">
                <span class="m-section-title-ico m-ico-title-tag-related">
                    <x-web.svg svg="ico-title-tag-related"></x-web.svg>
                </span>
                <span class="section-title-primary m-section-title-primary m-section-title-primary--medium">
                    {{ $post->main_tags->first()->hash_tag ?? ''}}
                </span>
                <span class="section-title-secondary m-section-title-secondary m-section-title-primary--medium">
                {{ __('pages/newmember.your_desk')  }}
                </span>

            </h2>

            <div class="read-more">
                <a href="{{route('web.search.ranking.tag.root', $post->main_tags->first())}}" class="read-more-inner">
                    <span class="read-more-text"> {{ __('pages/newmember.post_with_tag') }} </span>
                    <div class="read-more-btn">
                        <x-web.circle-r/>
                    </div>
                </a>
            </div>

            <div class="article-wrap">
                <div class="article-row">
                    @foreach($similarPosts as $post)
                        <?php
                        $post->username = !empty($post->username) ? $post->username : @$post->user->name;
                        ?>
                        <div class="article-col">
                            <x-web.cards.article :post="$post">
                                <span class="article-ranking article-ranking--{{$loop->iteration+1}}"></span>
                                <figure class="m-article-image">
                                    <x-web.lazy-img :src="$post->main_images->first()->url ?? ''" :alt="$post->username"
                                                    width="289" height="163"></x-web.lazy-img>
                                    @if(!empty($post->youtube))
                                        <x-web.youtube.youtube-icon
                                                class="detail-related-icon-youtube">
                                        </x-web.youtube.youtube-icon>
                                    @endif
                                </figure>
                            </x-web.cards.article>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="read-more-sp">
                <a href="{{route('web.search.ranking.tag.root', $post->main_tags->first())}}"
                   class="read-more-sp-inner">
                    <span class="read-more-sp-text">{{__("pages/newmember.post_with_tag")}}</span>
                    <div class="read-more-sp-btn">
                        <x-web.circle-r></x-web.circle-r>
                    </div>
                </a>
            </div>
        </div>
    </section>

    @include('web.shared.the-digital-diy')

    @include('web.shared.the-about')

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ url('assets/css/page/detail/style.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/page/detail/detail.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/page/detail/detail-mobile.css') }}">
@endpush

<!-- Push script -->
@push('scripts')
    <script type="text/javascript">
        load(); // Use php var in js

        function load() {
            window.NO_COMMENT_TEXT = '{{ $NO_COMMENT_TEXT }}';
            window.detail = {...@json($json) };
            window.APP_URL = "{{ config('app.url') }}";
            window.isLoggedIn = !!"{{ Auth::user() }}";
        }
        function HandleClickUserListPc() {
            $('#like-list-pc').show();
            $('#over-lay-user-like-pc').show()
        }
        function HandleClickUserListSp() {
            $('#like-list-sp').show();
            $('#over-lay-user-like-sp').show()
        }
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/swiper@6.4.8/swiper-bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="{{ asset('assets/js/page/commentBox.js')}}"></script>
    <script type="module" src="{{ asset('assets/js/page/detail/detail.js')}}"></script>
@endpush

@push('styles')
    <style>
        .comment-box {
            position: relative;
        }
        .btn-see-register {
            text-align: center;
            display: block;
            width: 100%;
            border-radius: 4px;
            padding: 10px;
            /*margin-top: 80px;*/
            margin-top: 45px;
            background-color: transparent;
            font-size: 14px;
            font-weight: 700;
            border: 1px solid #24d4d3;
            color: #24d4d3;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }
        .new-comment {
            position: relative;
        }
        .new-comment-mask {
            height: calc(100% - 55px);
            position: absolute;
            width: 100%;
            top: 0;
            background-image: linear-gradient(to top, rgba(26, 29, 33, 1), rgba(26, 29, 33, 0.3));
        }
        p.cmt {
            white-space: pre-wrap;
        }
        .spec-item-text > a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            font-family: Rajdhani;
            -webkit-transition: opacity 0.3s;
            transition: opacity 0.3s;
        }
        .spec-item-text > a:hover {
            opacity: 0.6;
        }
        .avatar {
            vertical-align: middle;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: inline-block !important;
        }
        .count-like-3 {
            margin-left: 43px;
        }
        .count-like-2 {
            margin-left: 23px;
        }
        .count-like-1 {
            margin-left: 3px;
        }
        .avatar-1 {
            z-index: 1;
            position: absolute;
            left: 20px;
        }
        .avatar-2 {
            z-index: 2;
            left: 40px;
            position: absolute;
        }
        .user-liked-list {
            cursor: pointer;
            position: relative;
            padding-bottom: 15px;
        }
        @media only screen and (max-width: 1024px) {
            .user-liked-list {
                padding-bottom: 0;
                margin: 15px;
                line-height: 25px;
            }
            .user-liked-list-desc {
                color: #FFFFFF;
            }
            .count-like {
                padding: 0 10px;
                color: #24d4d3;;
            }
        }
        /* Lap */
        @media (min-width: 768px) and (max-width: 1024px) {
            .new-comment-mask {
                height: calc(100% - 38px);
            }
        }

        /* Tablets */
        @media (min-width: 481px) and (max-width: 767px) {
            .new-comment-mask {
                height: calc(100% - 38px);
            }
        }

        /* Phone */
        @media (min-width: 381px) and (max-width: 480px) {
            .new-comment-mask {
                height: calc(100% - 38px);
            }
        }

        @media (min-width: 320px) and (max-width: 380px) {
            .new-comment-mask {
                height: calc(100% - 38px);
            }
            .btn-see-register {
                font-size: 12px;
            }
        }
    </style>
@endpush
