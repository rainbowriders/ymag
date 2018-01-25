<div ng-controller="RssController" ng-init="init({{ json_encode($feeds) }})">
    <card-box-rss title="{{ trans('rss.title') }}">
        <card-box-rss-actions>
            <div class="form-group">
                <h5>{{ trans('rss.settings') }}</h5>
            </div>
            <form ng-submit="savePreferences($parent.switchEditableMode)" novalidate name="form">
                <div class="btn-group" style="display: block">
                    <ul class="list-unstyled" role="menu" id="news-list">
                        {{--<li>--}}
                            {{--<label>--}}
                                {{--<input type="checkbox" ng-click="toggleAll($event)" ng-checked="allChecked">--}}
                                {{--{{ trans('rss.toggle') }}--}}
                            {{--</label>--}}
                        {{--</li>--}}
                        <li ng-repeat="feed in allFeeds">
                            <label style="display: block; padding-right: 10px;">
                                {{--<input type="checkbox" ng-click="trackUntrack(feed.id)" ng-checked="trackable(feed.id)">&nbsp;--}}
                                @{{ feed.name }}
                                <span class="pull-right text-danger">
                                    <strong>
                                        <i class="zmdi zmdi-close"
                                            data-toggle="modal"
                                            data-target=".bs-example-modal-sm"
                                            ng-click="confirmDeleteFeed(feed)"
                                            >
                                        </i>
                                    </strong>
                                </span>
                            </label>
                        </li>
                    </ul>
                </div>
                <div class="divider"></div>
                {{--<div class="form-group">--}}
                    {{--<button class="btn btn-primary" type="submit">{{ trans('buttons.save') }}</button>--}}
                    {{--<button class="btn btn-default" type="button" ng-click="cancel($parent.switchEditableMode)">{{ trans('buttons.cancel') }}</button>--}}
                {{--</div>--}}
            </form>
            <form ng-submit="addCustomRSSFeed(customFeedUrl, rssName)">
                <div class="form-group" ng-class="(rssValidLink == false) ? 'has-error has-feedback' : ''">
                    <input type="text" ng-model="customFeedUrl" placeholder="RSS Link" class="form-control" id="rss_url">
                    <div ng-if="rssValidLink == false">
                        <small class="text-danger">{{ trans('rss.invalid_link') }}</small>
                    </div>
                </div>
                <div class="form-group" ng-class="(rssValidName == false) ? 'has-error has-feedback' : ''">
                    <input type="text" ng-model="rssName" placeholder="RSS name" class="form-control" id="rss_name">
                    <div ng-if="rssValidName == false">
                        <small class="text-danger">{{ trans('rss.invalid_name') }}</small>
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn btn-custom">{{ trans('rss.add') }}</button>
                </div>
            </form>
        </card-box-rss-actions>
        <card-box-rss-body>
            <div style="overflow-y: auto;" ng-style="{'height': size1 + 'px'}">
                <div class="widget-user" ng-if="savedFeeds.length">
                    <a ng-href="@{{ article.link }}" target="_blank" ng-repeat="article in articles | orderBy: '-pubDate.date'" style="display: block; cursor: pointer">
                        <div class="m-b-15">
                            {{--<img ng-if="article.media == null && article.enclosure == null"src="images/noimage.png" style="width: 75px; height: auto; border: 1px solid #BFBFBF; margin-right: 15px;" alt="user">--}}
                            <img ng-if="article.media" ng-src="@{{ article.media.url }}" style="width: 75px; height: auto;" alt="user" class="rss-image">
                            <img ng-if="article.media == null && article.enclosure.url" ng-src="@{{ article.enclosure.url }}" style="width: 75px; height: auto;" alt="user" class="rss-image">
                            <div ng-class="{'wid-u-info': article.media}">
                                <strong class="m-t-0 m-b-5 font-600">@{{ article.title }}</strong>
                                <small class="text-default m-r-10 pull-right">@{{ article.pubDate.date | date: 'dd.MM.yyyy , HH:mm'}}</small>
                                <p class="text-muted m-b-5 font-13 rss-article-content">@{{ article.content }}</p>
                            </div>
                            <div style="height: 0px; clear: both; float: none;"></div>
                        </div>
                    </a>
                </div>

                <div class="widget-user" ng-if="!savedFeeds.length">
                    {{ trans('rss.no_feeds') }}
                </div>
            </div>
        </card-box-rss-body>
    </card-box-rss>

    <div class="modal fade bs-example-modal-sm in" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none; padding-right: 15px;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="mySmallModalLabel">Are you sure you wish to remove this feed?</h4>
                </div>
                <div class="modal-body">
                    <p><strong>@{{ junkFeed.name }}</strong> feed will be deleted from your news list </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal" ng-click="deleteFeed()">Delete this feed
                    </button>
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal" ng-click="cancelDeleteFeed()">Close
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
</div>

