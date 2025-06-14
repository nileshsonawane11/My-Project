<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="https://i.ibb.co/gLY2MgSd/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Add Event</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
            user-select : none;
        }

        :root {
            --primary-light: #FAC01F;
            --primary-dark: #F83900;
            --background : linear-gradient(90deg, var(--primary-light), var(--primary-dark));
        }
        body{
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            flex-direction: column;
        }
        .return{
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: row;
        }
        .return svg{
            cursor: pointer;
            
        }
        .container2{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            flex-wrap: wrap;
            width: 100%;
        }
        .txt{
            line-height: 20px;
        }
        .save-btn{
            width: 96px;
            height: 40px;
            display: flex;
            border-radius: 50px;
            align-items: center;
            justify-content: center;
            background: var(--background);
            color: #fff;
        }
        .get-info{
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            flex-wrap: wrap;
        }
        .info{
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 50px;
            overflow: hidden;
        }
        .schedule{
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            width: 100%;
            gap: 15px;
        }
        .check{
            width: 100%;
            position: relative;
        }
        .time{
            width: 100%;
            display: flex;
            gap: 15px;
            flex-direction: row;
        }
        textarea{
            overflow: hidden;
            resize: none;
            min-height: 30px;
            padding: 8px;
            font-size: 14px;
            width: 100%;
            border: none;
            border-bottom: solid 1px black;
            margin: 8px 0;
            padding: 10px 15px;
            font-size: 15px;
            width: 100%;
            outline: none;
            height: 45px;
            background: white;
        }
        #flexSwitchCheckChecked.form-check-input{
            height: 25px;
            width: 50px;
        }
        #flexSwitchCheckChecked.form-check-input:checked {
            background-color: #ff7100;
            border-color: #fd9f0d;
        }
        .form-check-input:focus {
            border-color:rgb(255, 111, 0);
            outline: 0;
            box-shadow: 0 0 0 .25rem rgba(253, 65, 13, 0.22);
        }
        .form-check-input{
            border: 1px solid rgb(255 89 0);
            transition: background-position .5s ease-in-out;
        }
        .form-switch{
            position: absolute;
            right: 3px;
            top: 15px;
        }
        .input-fields{
            width: 100%;
            position: relative;
        }
        .input-fields label{
            position: absolute;
            bottom: 22px;
            left: 14px;
            text-align: center;
            font-size: 16px;
            transition: 0.3s ease-in-out;
        }
        .input-fields #date,
        .input-fields #time {
            position: absolute;
            bottom: 22px;
            left: 14px;
            background: white;
            width: 100px;
            height: 20px;
            text-align: left;
            font-size: 16px;
            transition: 0.3s ease-in-out;
        }
        .input-fields input:valid + label,
        .input-fields input:focus + label {
            transform: translateX(-5px) translateY(-24px);
            font-size: 14px;
            color: black;
        }
        .input-fields textarea:valid + label,
        .input-fields textarea:focus + label {
            transform: translateX(-5px) translateY(-24px);
            font-size: 14px;
            color: black;
        }
        #dateInput input:valid + #date {
            transform: translateX(-5px) translateY(-24px);
            font-size: 14px;
            color: black;
        }
        .event-time{
            display: block;
            transition : width 0.5s ease-in-out;
        }
        .event-time.active{
            width: 0;
        }

        @media (min-width:601px) {
             .container{
                display: flex;
                background-color: #fff;
                position: relative;
                overflow-x: hidden;
                width: 90%;
                max-width: 100%;
                min-height: 100vh;
                align-items: center;
                justify-content: flex-start;
                flex-direction: column;
                gap: 30px;
                padding: 40px;
                scrollbar-width: none;
            }
            .container2{
                gap: 60px;
                width: 70%;
            }
            .container input[type="text"],[type="email"],[type="password"],[type="number"],[type="datetime"],[type="time"],[type="date"],select{
                border: none;
                border-bottom: solid 1px black;
                margin: 8px 0;
                padding: 10px 15px;
                font-size: 16px;
                width: 100%;
                outline: none;
                height: 45px;
                background: white;
            }
        }

        @media(max-width: 601px) {
            .container{
                display: flex;
                background-color: #fff;
                box-shadow: 0 5px 15px rgba(255, 255, 255, 0.35);
                position: relative;
                overflow-x: hidden;
                width: 768px;
                z-index: 0;
                max-width: 100%;
                min-height: 480px;
                padding: 40px 40px;
                height: 100vh;
                align-items: flex-start;
                justify-content: flex-start;
                flex-direction: column;
                gap: 30px;
            }
            .container2{
                gap: 40px;
            }
            .container input[type="text"],[type="email"],[type="password"],[type="number"],[type="datetime"],[type="time"],[type="date"],select{
                border: none;
                border-bottom: solid 1px black;
                margin: 8px 0;
                padding: 10px 15px;
                font-size: 15px;
                width: 100%;
                outline: none;
                height: 45px;
                background: white;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="return">
            <div><svg onclick="goBack()" width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M25.25 12.75H3.81247L13 21.9375L11.845 23.25L0.469971 11.875L11.845 0.5L13 1.8125L3.81247 11H25.25V12.75Z" fill="black"/>
                </svg></div>
            <div class="save-btn">Save</div>
        </div>
        
        <div class="container2">
            <div class="txt">
                <label for="">Team Name</label>
                <h4>Add Event</h4>
            </div>
            <div class="get-info">
                <div class="info">
                    <div class="schedule">
                        <div class="input-fields"><input type="date" id="dateInput" placeholder="Select Date" required><label for="dateInput" id="date">Date</label></div>
                        <div class="input-fields event-time"><input type="time" id="timeInput" placeholder="Select Time" required><label for="timeInput" id="time">Time</label></div>
                    </div>
                    <div class="input-fields"><input type="text" name="" id="opponent" required><label for="opponent">Opponent</label></div>
                    <div class="input-fields"><input type="text" name="" id="location" required><label for="location">Location</label></div>
                    <div class="check">
                        <input type="text" name="" id="" value="" placeholder="Full Day Event" readonly disabled>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" onclick="checkStatus()">
                        </div>
                    </div>
                    <div class="time">
                        <div class="input-fields"><input type="number" name="" id="duration" required><label for="duration">Duration</label></div>
                        <div class="input-fields"><input type="datetime" name="" id="arrival" required><label for="arrival">Arrival</label></div>
                    </div>
                    <div class="input-fields"><textarea name="" id="notes" placeholder="Description(Optional)" required></textarea></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        function goBack() {
            window.history.back();
        }

        //manage textarea
        const textarea = document.getElementById('notes');

        textarea.addEventListener('input', function(){
            this.style.height = 'auto';  // reset height
            this.style.height = (this.scrollHeight) + 'px';  // set new height
        });

        function checkStatus(){
            const checkbox = document.getElementById('flexSwitchCheckChecked');
            const timeInput = document.querySelector('.event-time');
            if(checkbox.checked){
                timeInput.classList.add('active');
                console.log('Checkbox is checked');
            } else {
                console.log('Checkbox is unchecked');
                timeInput.classList.remove('active');
            }
        }

        document.querySelector('.save-btn').addEventListener('click',()=>{
            window.location.href = './team-info.php';
        })

    </script>
</body>
</html>