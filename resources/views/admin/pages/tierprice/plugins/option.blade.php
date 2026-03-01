@php
    use App\Helpers\Form as FormTemplate;
    use Illuminate\Support\Carbon;
    use App\Helpers\Price as Price;
    $formInputAttr = config('configs.template.form_input');
    $formLabelAttr = config('configs.template.form_label');
    $dataType = ['' => '--- Please Select ---', '0' => 'Special Price', '1' => 'Special Percent'];
    $inputHiddenOptionDelete = html()->hidden('tier_price_option_delete', "")->attributes( ['id' => 'tier_price_option_delete']);
    $elementsOption = [
        [
            'label' => html()
                ->label(for: 'type', contents: 'Type')
                ->attributes(['class' => $formLabelAttr]),
            'element' => html()
                ->select('type', $dataType, @$item['type'])
                ->attributes(['class' => $errors->first('type') ? $formInputAttr . ' is-invalid' : $formInputAttr]),
        ],
    ];
@endphp
{!!$inputHiddenOptionDelete!!}
<div class="w-30 text-center mb-4">
    {!! FormTemplate::show($elementsOption) !!}
</div>
<hr>
<p><button type="button" id="add_option" class="btn  btn-success text-white"><i class="fa fa-plus-circle mr-2"></i>Add Tier
        Price</button></p>
<div class="w-50" id="loadValueTable"></div>
