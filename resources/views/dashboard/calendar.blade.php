<div ng-controller="CalendarController" ng-init="init({{ json_encode($calendars) }})">
    <card-box title="{{ trans('calendar.title') }}">
        <card-box-actions>
            <div class="form-group">
                <h5>{{ trans('calendar.title') }}</h5>
            </div>
            <form ng-submit="savePreferences($parent.switchEditableMode)" novalidate name="form">
                <div class="btn-group" style="display: block">
                    <ul class="list-unstyled" id="calendars-list">
                        <li ng-repeat="cal in calendars">
                            <label>
                                <input type="checkbox" name="calendar" ng-checked="cal.selected" ng-click="select(cal)">&nbsp;
                                @{{ cal.summary }}
                            </label>
                        </li>
                    </ul>
                </div>
                <div class="divider"></div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">{{ trans('buttons.save') }}</button>
                    <button class="btn btn-default" type="button" ng-click="cancel($parent.switchEditableMode)">{{ trans('buttons.cancel') }}</button>
                </div>
            </form>
        </card-box-actions>
        <card-box-body>
            <div style="overflow-y: auto;" ng-style="{'height': size2 + 'px'}">
                <table class="table">
                    <tr ng-if="hasEvents == false">
                        <td colspan="2" class="calendar-date">
                            {{ trans('calendar.no_events') }}
                        </td>
                    </tr>
                    <tr ng-repeat="item in calendarEvents | orderBy: 'date'" ng-if="item.date > yesterday">
                        <td class="text-primary calendar-date">
                            @{{ item.date | date:'MMM dd, yyyy'}}
                        </td>
                        <td>
                            <ul class="list-unstyled news-list">
                                <li ng-repeat="event in item.events | orderBy: '-allDay'">
                                    <event-icon event="@{{ event }}"></event-icon>
                                    <span ng-if="event.allDay">{{ trans('calendar.all_day') }}</span>
                                    <span ng-if="! event.allDay">
                                        @{{ event.start.time }} - @{{ event.end.time }}
                                    </span>
                                    &nbsp;
                                    <a ng-href="@{{ event.link }}" target="_blank">@{{ event.summary || 'No Title' }}</a>
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
            </div>
        </card-box-body>
    </card-box>
</div>