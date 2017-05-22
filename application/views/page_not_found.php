<html>
    <head>
        <title>Jinx - Page not found.</title>
    </head>
    
    <style>
        table{
            margin: 0 auto;
            border:1px solid;
            font-family: sans-serif;
        }
        .error{
            color: red;
            font-size: 12px;
            margin: 2px 0 0 0;
        }
        td.headertop{
            height: 200px;
        }
        div.headerbg{
            /*height: 100%;*/
        }
        div.logo{
            padding-top: 20px;
            padding-bottom: 20px;
            text-align: center;
            background: #000;
        }
        div.headerinner{
            height: 100px;
            margin: 10px auto 0;
            display: block;
            position: relative;
            width: 100px;
        }
        div.header-bottom{
            margin-bottom: 65px;
        }
        div.headerinner span {
            color: #ffffff;
            display: block;
            left: 15px;
            margin: 26px 0 0;
            position: absolute;
            text-align: center;
            top: 0;
        }        
    </style>
    <body>
        
            
            
            <table cellspacing="0" cellpadding="0" style="max-width: 900px;width: 100%;">
                <tr>
                    <td colspan="2" class="headertop">
                        <div class="headerbg">
                            <div class="logo"><img src="<?php echo $this->config->item('front_assets');?>images/top-header.png" height="100" /></div>
                            
                        </div>
                        <div class="header-bottom"></div>
                    </td>
                </tr>
                
                
                <tr>
                    <td>
                        <center>
                            <h1>
                                PAGE NOT FOUND
                            </h1>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td>
                        <center>
                            Sorry, the page you are looking for does not exist.
                        </center>
                    </td>
                </tr>
                <tr>
                    <td style="height: 30px;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" style="height:30px;">&nbsp;</td>
                </tr>
                <tr>
                    <td bgcolor="#3C3E3F" colspan="2" style="height:30px;"></td>
                </tr>
            </table>
    </body>
</html>