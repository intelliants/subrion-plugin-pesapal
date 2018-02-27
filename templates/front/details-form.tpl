{ia_print_css files='_IA_URL_modules/pesapal/templates/front/css/style'}

<div id="pesapal-form">
    <form action="#" method="post" class="form-horizontal">
        {preventCsrf}
        <div class="form-group">
            <label class="col-sm-4 control-label" for="amount">{lang key='pesapal_amount'} <span class="is-required">*</span></label>
            <div class="col-sm-6">
                <input type="text" autocomplete="off" class="form-control" id="amount" name="amount" value="{$transaction.amount}">
            </div>
        </div>

        <input type="hidden" id="type" name="type" value="MERCHANT">
        <input type="hidden" id="type" name="reference" value="{$transaction.sec_key}">

        <div class="form-group">
            <label class="col-sm-4 control-label" for="description">{lang key='pesapal_description'} <span class="is-required">*</span></label>
            <div class="col-sm-6">
                <input type="text" autocomplete="off" class="form-control" id="description" name="description" value="{$transaction.operation}">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="first-name">{lang key='pesapal_first_name'} <span class="is-required">*</span></label>
            <div class="col-sm-6">
                <input type="text" autocomplete="off" class="form-control" id="first-name" name="first-name">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="last-name">{lang key='pesapal_last_name'} <span class="is-required">*</span></label>
            <div class="col-sm-6">
                <input type="text" autocomplete="off" class="form-control" id="last-name" name="last-name">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="email">{lang key='pesapal_email'} <span class="is-required">*</span></label>
            <div class="col-sm-6">
                <input type="email" autocomplete="off" class="form-control" id="email" name="email">
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-offset-4 col-md-2">
                <button type="submit" name="#" class="btn btn-primary">{lang key='pesapal_proceed'}</button>
            </div>
        </div>
    </form>
</div>