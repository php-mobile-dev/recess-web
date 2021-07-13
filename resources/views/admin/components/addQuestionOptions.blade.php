<div class="form-group">
    <label>Initial Question</label>
    <input type="text" class="form-control" placeholder="Enter Initial Question" name="initial_question" required="required" value="{{($question) ? $question->question : ''}}">
</div>
<div class="form-group">
    <label>Options</label>
    <input type="text" class="form-control" placeholder="Enter options" name="question_options" required="required" value="{{isset($options) ? $options : ''}}">
    <p class="help-block">Press , or enter to add chips</p>
</div>

<script src="{{asset('js/autocomplete.js')}}"></script>
<script>
    $(function(){
        var $input_tagator1 = $('input[name="question_options"]');
        // $input_tagator1.click(function () {
            if ($input_tagator1.data('tagator') === undefined) {
                $input_tagator1.tagator({
                    autocomplete: [],
                    useDimmer: true
                });
                // $activate_tagator1.val('destroy tagator');
            } else {
                $input_tagator1.tagator('destroy');
                // $activate_tagator1.val('activate tagator');
            }
        // });
        // $activate_tagator1.trigger('click');
    });
    
</script>