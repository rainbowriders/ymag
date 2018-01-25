<div ng-controller="GmailController">
    <card-box title="{{ trans('gmail.title') }}">
        <card-box-actions>
            <div class="form-group">
                <h5>{{ trans('gmail.settings') }}</h5>
            </div>
            <form ng-submit="savePreferences($parent.switchEditableMode)" novalidate name="form" class="form-horizontal">
                <div ng-show="!searchMode">
                    <div class="form-group">
                        <div class="input-group">
                            <input ng-focus="toggleSearchMode(true)" value="@{{ query }}" type="text" class="form-control" placeholder="{{ trans('gmail.search') }}">
                            <span class="input-group-btn">
                                <button type="button" disabled class="btn waves-effect waves-light btn-primary">
                                    <i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div ng-show="searchMode">
                    <div class="form-group">
                        <input type="text" autofocus class="form-control input-sm" placeholder="{{ trans('gmail.from') }}" ng-model="filter.from"/>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control input-sm" placeholder="{{ trans('gmail.to') }}" ng-model="filter.to"/>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control input-sm" placeholder="{{ trans('gmail.subject') }}" ng-model="filter.subject"/>
                    </div>
                    <div class="form-group">
                        <input id="gmail_include_spam" type="checkbox" ng-model="filter.includeSpamTrash">&nbsp;
                        <label for="gmail_include_spam">{{ trans('gmail.include_spam') }}</label>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">{{ trans('buttons.search') }}</button>
                    <button class="btn btn-default" type="button" ng-click="toggleSearchMode(false, $parent.switchEditableMode)">{{ trans('buttons.cancel') }}</button>
                </div>
            </form>
        </card-box-actions>
        <card-box-body>
            <div style="overflow-y: auto;" ng-style="{'height': size2 + 'px'}" ng-class="{'mail-empty' : showBackground}">
                <div ng-if="message">
                    <a ng-click="backToList()" class="btn btn-default">
                        <i class="zmdi zmdi-long-arrow-return"></i>
                        {{ trans('buttons.back') }}

                    </a>
                    <a ng-href="https://mail.google.com/mail/u/0/#inbox/@{{ message.id }}" target="_blank" class="btn btn-link">
                        <i class="class zmdi zmdi-swap"></i>
                        {{ trans('buttons.view_in_gmail') }}
                    </a>
                    <br/><br/>

                    <table class="table">
                        <tr>
                            <td>{{ trans('gmail.from') }}</td>
                            <td>@{{ message.from[0] }} @{{ message.from[1] }}></td>
                        </tr>
                        <tr>
                            <td>{{ trans('gmail.subject') }}</td>
                            <td>@{{ message.subject }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <iframe id="msg-iframe" height="0" width="100%" ng-src="@{{ fullMessageUrl(message.id) }}" frameborder="0" scrolling="0"></iframe>
                            </td>
                        </tr>
                    </table>
                    <div class="clearfix"></div>
                </div>

                <div style="overflow: hidden">
                    <ul class="list-group">
                         <li class="g-message-list-item list-group-item" ng-repeat="message in messages" >
                            <a ng-href="https://mail.google.com/mail/u/0/#inbox/@{{ message.id }}" target="_blank" style="color: rgb(121, 121, 121);display: block;">
                                {{--<small class="label label-default pull-right">@{{ message.date }}</small>--}}
                                <strong class="msg-from pull-left">
                                    @{{ message.from[1] || message.from[0] }}
                                </strong>
                                <br class="clearfix"/>
                                <span class="msg-subject text-dark" ng-class="{'font-bold': isUnRead(message)}">
                                    @{{ message.subject }}
                                </span>
                                <small class="text-default m-r-10 pull-right" style="color: #337ab7;">@{{ message.date | date: 'dd.MM.yyyy , HH:mm'}}</small>
                                {{--<br/>
                                <small class="msg-snippet text-muted" ng-bind-html="message.snippet"></small>--}}
                                <br class="clearfix"/>
                            </a>
                        </li>
                        {{--<li class="g-message-list-item list-group-item" ng-if="messages.length <= 0" id="empty">--}}
                            {{--<img src="images/mail-empty.jpg" alt="" style="width: 100%; height: auto">--}}
                        {{--</li>--}}
                    </ul>
                    <div>
                        <button ng-if="messages.length >= 10 && nextPageToken" ng-click="next()" class="btn btn-default btn-block">
                            {{ trans('buttons.more') }}
                        </button>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <div class="clearfix"></div>
            </div>
        </card-box-body>
    </card-box>
</div>