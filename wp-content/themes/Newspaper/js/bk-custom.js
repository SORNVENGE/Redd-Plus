
var i = 0;
function functionUploadFile(fileName, labelName) {
    i++;
    if(fileName == 'upload_document'){
        var input = '<input type="file" class="form-control-file " id="upload_document_'+i+'" name="upload_document[]" accept="application/pdf,application/vnd.ms-excel" >';
    }else if(fileName == 'upload_kml'){
        var input = '<input type="file" class="form-control-file " id="upload_document_'+i+'" name="upload_kml[]" accept=".kml" >';
    }else if(fileName == 'upload_optional'){
        var input = '<input type="file" class="form-control-file " id="upload_document_'+i+'" name="upload_optional[]" accept="application/pdf,application/vnd.ms-excel" >';
    }else if(fileName == 'upload_mrv'){
        var input = '<input type="file" class="form-control-file " id="upload_document_'+i+'" name="upload_mrv[]" accept="application/pdf,application/vnd.ms-excel" >';
    }else if(fileName == 'upload_safeguard'){
        var input = '<input type="file" class="form-control-file " id="upload_document_'+i+'" name="upload_safeguard[]" accept="application/pdf,application/vnd.ms-excel" >';
    }else if(fileName == 'upload_benefit'){
        var input = '<input type="file" class="form-control-file " id="upload_document_'+i+'" name="upload_benefit[]" accept="application/pdf,application/vnd.ms-excel" >';
    }
    // console.log(input);
    // multiple
    // var removeLink = "<a class=\"removeFile\" href=\"#\" data-fileid=\"" + i + "\">Remove</a>";
    jQuery('.'+labelName).children(".fileList").append("<li id='needInput"+i+"'>"+input+"</li> ");

    document.getElementById('upload_document_'+i).click();
    document.getElementById('upload_document_'+i).addEventListener("change", function (data) {
        var files = data.target.files;
        // var filenames = document.getElementById(labelName);
        // filenames.innerHTML = ""
        
        if (files.length > 0) {
            var output = [];
            var p = 0;
            Array.from(files).forEach((file) => {
                p++;
                var removeLink = "<a class=\"removeFile\" href=\"#\" data-fileid=\"" + i + "\" inputID='"+fileName+"'>X</a>";
                
                if(p == 1){ 
                    output.push(removeLink+" <strong>", escape(file.name), "</strong>, ");
                }else{
                    output.push(" <strong>", escape(file.name), "</strong>");
                }
                
                // var input = '<input type="file" class="form-control-file hiddenFile" id="upload_document_'+p+'" name="upload_documentFile[]" accept="application/pdf,application/vnd.ms-excel">';
                // filenames.innerHTML += input+file.name + ' , ';
            })
            // console.log(output);
            jQuery('.'+labelName).find("li#needInput"+i).append(output.join(""));
            jQuery("#"+fileName).removeClass('require');

            var class_div = '#blockStep2';
            if (fileName == "upload_document") {
                jQuery(class_div + ' #title-upload-document').removeClass('label_red');
            }
            else if (fileName == "upload_kml") {
                jQuery(class_div + ' #project-location').removeClass('label_red');
            }
            else if (fileName == "upload_optional") {
                jQuery(class_div + ' #document-optional').removeClass('label_red');
            }
            else if (fileName == "upload_mrv") {
                jQuery(class_div + ' #display-name-upload_mrv').removeClass('border_label_red');
            }
            else if (fileName == "upload_safeguard") {
                jQuery(class_div + ' #display-name-safeguard').removeClass('border_label_red');
            }
            else if (fileName == "upload_benefit") {
                jQuery(class_div + ' #display-name-benefit').removeClass('border_label_red');
            }
            else if (fileName == "greenhouse_gases") {
                jQuery(class_div + ' #label-greenhouse').removeClass('label_red');
            }
            else {
                jQuery(class_div + ' #' + id).prev('label').removeClass('label_red');
            }
        }else{
            jQuery('.'+labelName).find("li#needInput"+i).remove();
        }
    });
}


(function ($) {
    

    $(document).ready(function () {
        $(document).on("click",".removeFile", function(e){
            e.preventDefault();
            var idAdd = $(this).attr('inputID');
            var main = $(this).parent().parent();
            $(this).parent().remove();
            if(main.find('li').length == 0){
                jQuery("#"+idAdd).addClass('require');
            }

            // jQuery("#"+idAdd).addClass('require');
            // $(this).parent().remove();
        });

        $(document).on("keyup","input.phone_number", function(e){
            this.value = this.value.replace(/[^0-9\.]/g,'');
        });
        
        



        fun_validate = function (class_div) {
            var num;
            num = 0;
            $(class_div + " .require").each(function (index, element) {
                var id, info, value;
                value = $(element).val();
                id = $(element).attr('id');

                if (value == '') {
                    
                    console.log(id + '=>' + value);
                    $('#field-require').addClass('display');
                    if (id == "upload_document") {
                        $(class_div + ' #title-upload-document').addClass('label_red');
                    }
                    else if (id == "upload_kml") {
                        $(class_div + ' #project-location').addClass('label_red');
                    }
                    else if (id == "upload_optional") {
                        $(class_div + ' #document-optional').addClass('label_red');
                    }
                    else if (id == "upload_mrv") {
                        $(class_div + ' #display-name-upload_mrv').addClass('border_label_red');
                    }
                    else if (id == "upload_safeguard") {
                        $(class_div + ' #display-name-safeguard').addClass('border_label_red');
                    }
                    else if (id == "upload_benefit") {
                        $(class_div + ' #display-name-benefit').addClass('border_label_red');
                    }
                    else {
                        if (id == "greenhouse_gases") {
                            $(class_div + ' #label-greenhouse').addClass('label_red');
                        } else {
                            $(this).prev('label').addClass('label_red');
                            // $(class_div + ' #' + id).prev('label').addClass('label_red');
                        }
                    }

                    num = num + 1;
                } else {
                    $('#field-require').removeClass('display');
                    if (id === 'emails' || id === "email-partner" || id === "email-pp") {
                        pattern = /^[0-9a-z\._\-]+@[a-zA-Z_\-]+?\.[a-zA-Z]{2,}?(\.[a-zA-Z]{2,})?$/
                        if (!pattern.test(value)) {
                            // $(class_div+' #' + id).parents().find('label').addClass('label_red');
                            $(class_div + ' #' + id).prev('label').addClass('label_red');
                            num = num + 1;
                        } else {
                            $(class_div + ' #' + id).prev('label').removeClass('label_red');
                        }
                    } else {
                        // console.log(id);
                        if (id == "upload_document") {
                            $(class_div + ' #title-upload-document').removeClass('label_red');
                        }
                        else if (id == "upload_kml") {
                            $(class_div + ' #project-location').removeClass('label_red');
                        }
                        else if (id == "upload_optional") {
                            $(class_div + ' #document-optional').removeClass('label_red');
                        }
                        else if (id == "upload_mrv") {
                            $(class_div + ' #display-name-upload_mrv').removeClass('border_label_red');
                        }
                        else if (id == "upload_safeguard") {
                            $(class_div + ' #display-name-safeguard').removeClass('border_label_red');
                        }
                        else if (id == "upload_benefit") {
                            $(class_div + ' #display-name-benefit').removeClass('border_label_red');
                        }
                        else if (id == "greenhouse_gases") {
                            $(class_div + ' #label-greenhouse').removeClass('label_red');
                        }
                        else {
                            $(class_div + ' #' + id).prev('label').removeClass('label_red');
                        }

                    }
                }
            });
            if (num > 0) {
                return 1;
            } else {
                return 0;
            }
        };
        $('.checkboxEvent').click(function (e) {
            var id = $(this).attr('id');
            if (this.checked) {
                $("#" + id + "File").find('input').addClass('require');
                $("#" + id + "File").show();
                if (id == "mrv") {
                    // $('#label-related').addClass('require');
                }
                else if (id == "safeguard") {
                    // $('#label-safeguard').addClass('require');
                }
                else if (id == "benefit") {
                    // $('#label-benefit').addClass('require');
                }

            } else {
                $("#" + id + "File").hide();
                if (id == "mrv") {
                    $('#label-related').removeClass('require');
                }
                else if (id == "safeguard") {
                    $('#label-safeguard').removeClass('require');
                }
                else if (id == "benefit") {
                    $('#label-benefit').removeClass('require');
                }
                $("#" + id + "File").find('input').removeClass('require');
                $("#" + id + "File").find(input).val('');
            }

        });
        // custome date picker 
        $('.bootstrap-date').datepicker({
            format: 'dd/mm/yyyy'
        });
        $('.bootstrap-date-range').datepicker({
            format: 'dd/mm/yyyy',
            inputs: $('.actual_range')
        });
        // $('#project_end').datepicker({
        //     format: 'dd/mm/yy'
        // });

        $('.next').click(function (e) {
            if (fun_validate('#blockStep1') == 0) {
                $('#blockStep1').submit();
            }
        })
        $('#next-step').click(function (e) {
            // console.log(fun_validate('#blockStep2'))
            if (fun_validate('#blockStep2') == 0) {
                $("#next-step").hide();
                $(".loader_img").css('display','inline-block');
                $('#blockStep2').submit();
            }
            
        })

        $( ".input" ).focus(function() {
            // $('.borderleft').remove();
            $(this).after('<div class="borderleft"></div>');
        });
        $( ".input" ).focusout(function() {
            $('.borderleft').remove();
        });

        $( ".login-remember label" ).append('<label></label>');

        $('#loginFormSubmit').click(function (e) {
            if (fun_validate('#formlogin') == 0) {
                $('#formlogin').submit();
            }
            return false;
        })
    
    });
})(jQuery);

document.getElementById("id-substring").addEventListener("click", myFunction);

function myFunction() {
    alert ("Hello World!");
  }