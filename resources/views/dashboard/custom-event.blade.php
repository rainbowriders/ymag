<div ng-controller="CustomEventController">
    <card-box title="{{ trans('customEvent.title') }}">
        <card-box-actions>
            <div class="form-group">
                <h5>{{ trans('customEvent.settings') }}</h5>
            </div>
            <form ng-submit="createEvent($parent.switchEditableMode, event.title, hours, minutes, seconds)">
                <div class="input-group events-full-width-inputs" ng-class="eventError.eventTitle ? 'has-error has-feedback' : ''">
                    <input type="text" class="form-control" placeholder=" {{ trans('customEvent.name') }} " id="event-title" ng-model="event.title" ng-change="eventError.eventTitle = ''">
                    <div ng-if="eventError.eventTitle"><span>
                            <small class="text-danger">@{{ eventError.eventTitle }}</small>
                        </span>
                    </div>
                </div>
                <div class="input-group events-full-width-inputs" ng-class="eventError.eventDate ? 'has-error has-feedback' : ''">
                    <input type="text" class="form-control" placeholder="dd.mm.yyyy" id="datepicker-autoclose" data-provide="datepicker" value="@{{ eventDateToString }}">
                    <div ng-if="eventError.eventDate"><span>
                            <small class="text-danger">@{{ eventError.eventDate }}</small>
                        </span>
                    </div>
                </div>
                <div class="input-group events-hh-mm-ss-inputs" ng-if="options.selectedTime != 3">
                    <input type="number" class="form-control" placeholder="hh" id="custom-event-hours" min="0" max="24" value="@{{ event.time |date:'HH' }}">
                    <input type="number" class="form-control" placeholder="mm" id="custom-event-minutes" min="0" max="60" value="@{{ event.time |date:'mm' }}">
                    <input type="number" class="form-control" placeholder="ss" id="custom-event-seconds" min="0" max="60" value="@{{ event.time |date:'ss' }}">
                </div>
                <div ng-if="eventError.invalidTime">
                    <span>
                        <small class="text-danger">@{{ eventError.invalidTime }}</small>
                    </span>
                </div>
                <div class="radio">
                    <input type="radio" name="radio" id="radio1" ng-click="setSelectedValue(1)" ng-checked="options.selectedTime == 1">
                    <label for="radio1">
                        {{ trans('customEvent.selectedOne') }}
                    </label>
                </div>
                <div class="radio">
                    <input type="radio" name="radio" id="radio2" ng-click="setSelectedValue(2)" ng-checked="options.selectedTime == 2">
                    <label for="radio2">
                        {{ trans('customEvent.selectedTwo') }}
                    </label>
                </div>
                <div class="radio">
                    <input type="radio" name="radio" id="radio3"  ng-click="setSelectedValue(3)" ng-checked="options.selectedTime == 3">
                    <label for="radio3">
                        {{ trans('customEvent.selectedTree') }}
                    </label>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit" ng-if="event != null">Save</button>
                    <button class="btn btn-primary" type="submit" ng-if="event == null">{{ trans('customEvent.add') }}</button>
                    <button class="btn btn-default" type="button" ng-click="cancel($parent.switchEditableMode)">Cancel</button>
                </div>
            </form>
        </card-box-actions>
        <card-box-body>
            <div style="overflow-y: auto;" ng-style="{'height': size3 + 'px'}" style="overflow-y: auto">
                <div ng-if="event != null">
                    <h4 class="text-dark text-center"><strong>@{{ event.title }}</strong></h4>
                    <p class="text-center">@{{ eventTimeToString }}</p>
                    <p ng-if="loading == false" class="text-center">
                        <strong>@{{ timeLeftToString }}</strong>
                    </p>
                </div>
                <div ng-if="event == null">
                    <h4 class="text-center text-dark">{{ trans('customEvent.not_event') }}</h4>
                </div>
            </div>
        </card-box-body>
    </card-box>
</div>