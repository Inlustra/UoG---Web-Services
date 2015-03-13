$.validator.addMethod("regex", function(value, element, regexp) {
    var regex = new RegExp(regexp);
    return this.optional(element) || regex.test(value);
}, 'Format is invalid');
function valid_postcode(postcode) {
    postcode = postcode.replace(/\s/g, "");
    var regex = /^[A-Z]{1,2}[0-9]{1,2} ?[0-9][A-Z]{2}$/i;
    return regex.test(postcode);
}
$.validator.addMethod("postcode", function(value, element) {
    var postcode = value.toUpperCase().replace(" ", "").trim();
    console.log('Testing: '+postcode+ " "+valid_postcode(postcode));
    return this.optional(element) ||valid_postcode(postcode);
}, 'Format is invalid');