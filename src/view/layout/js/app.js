$(document).ready(function() {
    
    $('#Province').on('change' , function(){
        var province_id = $(this).val();
        if(province_id){
            $.ajax({
                url : 'http://localhost/Web2/src/controller/db_controller/getDistrict.php',
                method : 'GET',
                dataType : 'json',
                data: {
                    province_id : province_id
                },
                success: function(data) {
                    $('#District').empty();
                    console.log(data);
                    $.each(data , function(i,district){
                        $('#District').append($('<option>' , {
                            value: district.id,
                            text: district.name
                        }));
                    });
                    $('#Ward').empty();
                },
                error: function(xhr , txtStatus , errorThrown){
                    console.log('Error : '+errorThrown);
                }
            });
            $('#Ward').empty();
        }else{
            $('#District').empty();
            $('#Ward').empty();
        }
    });
    
    $('#District').on('change' , function(){
        var district_id = $(this).val();
        if(district_id){
            $.ajax({
                url : 'http://localhost/Web2/src/controller/db_controller/getWard.php',
                method : 'GET',
                dataType : 'json',
                data: {
                    district_id : district_id
                },
                success: function(data) {
                    $('#Ward').empty();
                    console.log(data);
                    $.each(data , function(i,ward){
                        $('#Ward').append($('<option>' , {
                            value: ward.id,
                            text: ward.name
                        }));
                    });
                },
                error: function(xhr , txtStatus , errorThrown){
                    console.log('Error : '+errorThrown);
                }
            });
        }else{
            $('#Ward').empty();
        }
    });
});