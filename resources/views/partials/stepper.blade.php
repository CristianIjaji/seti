
<style>
    .steps .step {
        display: block;
        width: 100%;
        text-align: center
    }
    .steps .step .step-icon-wrap {
        display: block;
        position: relative;
        width: 100%;
        height: 40px;
        text-align: center
    }
    .steps .step .step-icon-wrap::before,
    .steps .step .step-icon-wrap::after {
        display: block;
        position: absolute;
        top: 50%;
        width: 50%;
        height: 3px;
        margin-top: -1px;
        background-color: #e1e7ec;
        content: '';
        z-index: 1
    }
    .steps .step .step-icon-wrap::before {
        left: 0
    }
    .steps .step .step-icon-wrap::after {
        right: 0
    }
    .steps .step .step-icon {
        display: inline-block;
        position: relative;
        width: 40px;
        height: 40px;
        border: 1px solid #e1e7ec;
        border-radius: 50%;
        background-color: #f5f5f5;
        color: #374250;
        font-size: 20px;
        line-height: 40.5px;
        z-index: 5
    }
    .steps .step .step-title {
        margin-top: 16px;
        margin-bottom: 0;
        color: #606975;
        font-size: 14px;
        font-weight: 500
    }
    .steps .step:first-child .step-icon-wrap::before {
        display: none
    }
    .steps .step:last-child .step-icon-wrap::after {
        display: none
    }
    .steps .step.completed .step-icon-wrap::before,
    .steps .step.completed .step-icon-wrap::after {
        background-color: #0da9ef
    }
    .steps .step.completed .step-icon {
        border-color: #0da9ef;
        background-color: #0da9ef;
        color: #fff
    }

    @media (max-width: 576px) {
        .flex-sm-nowrap .step .step-icon-wrap::before,
        .flex-sm-nowrap .step .step-icon-wrap::after {
            display: none
        }
    }

    @media (max-width: 768px) {
        .flex-md-nowrap .step .step-icon-wrap::before,
        .flex-md-nowrap .step .step-icon-wrap::after {
            display: none
        }
    }

    @media (max-width: 991px) {
        .flex-lg-nowrap .step .step-icon-wrap::before,
        .flex-lg-nowrap .step .step-icon-wrap::after {
            display: none
        }
    }

    @media (max-width: 1200px) {
        .flex-xl-nowrap .step .step-icon-wrap::before,
        .flex-xl-nowrap .step .step-icon-wrap::after {
            display: none
        }
    }

    .bg-faded {
        background-color: #f5f5f5 !important;
    }
</style>
<div class="container border-bottom mb-3">
    <div>
        <div class="card-body pb-3">
            <div class="steps d-flex flex-wrap flex-sm-nowrap justify-content-between">
                @foreach ($steps as $step)
                    <div class="step {{ $step['completed'] }}">
                        <div class="step-icon-wrap">
                            <div class="step-icon" data-toggle="tooltip" data-placement="top" title="{{ $step['time'] }}">{!! $step['icon'] !!}</div>
                        </div>
                        <h4 class="step-title">{{ $step['title'] }}</h4>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>