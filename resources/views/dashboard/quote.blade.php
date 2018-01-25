<div ng-controller="QuoteController">
    <card-box title="{{ trans('quote.title') }}" ng-init="quote={{ json_encode($quote) }}">
        <card-box-body>
            <div ng-style="{'height': size3 + 'px'}" style="overflow-y: auto">
                <div ng-if="! quote.id">
                    {{ trans('quote.not_found') }}
                </div>
                <div ng-if="quote.id">
                    <div class="well2 p-l-r-10">
                        <span class="quote-text">@{{ quote.quote }}
                        &nbsp;&nbsp;</span>
                        <br>
                        <span class="quote-author">- @{{ quote.author }}</span>
                        <button class="btn btn-link quote-btn" ng-click="fetchRandom()">
                            {{ trans('buttons.more') }}
                            <i class="zmdi zmdi-long-arrow-right"></i>
                        </button>
                    </div>

                </div>
            </div>
        </card-box-body>
    </card-box>
</div>