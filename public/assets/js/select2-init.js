(function ($) {
    "use strict";
    $(function () {
        if (typeof $.fn.select2 === "undefined") {
            console.error(
                "Select2 belum diload — periksa urutan <script> (select2.min.js harus sebelum init)."
            );
            return;
        }

        if ($(".js-example-basic-single").length) {
            $(".js-example-basic-single").select2({
                placeholder: "-- Pilih --",
                allowClear: true,
                width: "100%",
            });
        }

        if ($(".js-example-basic-multiple").length) {
            $(".js-example-basic-multiple").select2({
                placeholder: "-- Pilih beberapa --",
                allowClear: true,
                width: "100%",
            });
        }
    });
})(jQuery);
