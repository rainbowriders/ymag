<div ng-controller="WeatherController">
    <card-box title="{{ trans('weather.title') }}">
        <card-box-actions>
            <div class="form-group">
                <h5 >{{ trans('weather.settings') }}</h5>
            </div>
            <form ng-submit="savePreferences($parent.switchEditableMode);" novalidate name="form">
                <div class="form-group text-left">
                    {{--<p class="text-muted font-13 m-b-15 m-t-20">{{ trans('weather.units') }}</p>--}}
                    <div class="radio radio-info radio-inline">
                        <input type="radio" required id="celsius" value="si" ng-model="filter.units">
                        <label for="celsius">{{ trans('weather.celsius') }}</label>
                    </div>
                    <div class="radio radio-info radio-inline">
                        <input type="radio" required id="fahrenheit" value="us" ng-model="filter.units">
                        <label for="fahrenheit">{{ trans('weather.fahrenheit') }}</label>
                    </div>
                </div>
                <div class="btn-group">
                    <input type="text" required class="form-control" placeholder="{{ trans('weather.location') }}" ng-model="filter.address"/>

                    <ul style="position: absolute;" id="cities-list" class="dropdown-menu demo-dropdown" role="menu" ng-if="cities.length">
                        <li ng-repeat="city in cities">
                            <a ng-click="selectCity(city)">@{{ city.description }}</a>
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
            <div ng-style="{'height': size1 + 'px'}" style="overflow-y: auto; overflow-x: hidden">
                <div class="row p-l-r-10" ng-show="weather && weather.timezone">
                    <div class="row weather-data-container">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            <div class="col-md-12 col-lg-12 text-center">
                                <img  ng-src="@{{ currentHourIcon }}" alt="" class="current-weather-icon">
                                <h4 class="text-primary">@{{ currentHourSummary }}</h4>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
                            {{--<img ng-if="weather.currently.icon" ng-src="@{{ icon() }}" width="100" height="100" alt="">--}}

                            <h1 class="text-primary" style="margin-bottom: 5px">@{{ currnetHourTemperature | number:0 }} @{{ (filter.units == 'us' ? '&deg;F' : "&deg;C") }}</h1>
                            <h4 class="text-primary" style="margin-top: 0">@{{ city }}</h4>
                        </div>
                    </div>
                    <div class="row" id="hourly-boxes">
                        <div class="">
                            <span  ng-repeat="w in weather.hourly.data | limitTo: startIndex" ng-if="showThisHour(w)">
                                <div class="hourly-temp-boxes text-center" style="display: inline-block;">
                                    <div>
                                        <span style="color: black">@{{ getTimeToDate(w.time) | date:'HH:mm' }}</span>
                                    </div>
                                    <div title="@{{ w.summary }}">
                                        <img ng-src=" @{{ icon(w.icon) }} " alt="" width="30" height="30">
                                    </div>
                                    <div>
                                        <span class="text-primary">@{{ w.temperature | number:0 }} @{{ (filter.units == 'us' ? '&deg;F' : "&deg;C") }}</span>
                                    </div>
                                </div>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </card-box-body>
    </card-box>
</div>