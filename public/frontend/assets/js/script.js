const site_url = "https://baolinhkhochay.io.vn/";
$("body").on("keyup", "#search", function () {
	let text = $("#search").val();
	// console.log(text);
	if (text.length > 0) {
		$.ajax({
			data: { search: text },
			url: site_url + "search-product",
			method: 'post',
			beforSend: function (request) {
				return request.setRequestHeader('X-CSRF-TOKEN', ("meta[name='csrf-token']"));
			},
			success: function (result) {
				$("#searchProducts").html(result);
			}
		});
	}
	if (text.length < 1) {
		$("#searchProducts").html(" ");
	}
});
