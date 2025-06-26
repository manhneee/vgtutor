<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>404 Land</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: url('background404.png') no-repeat center bottom;
            background-size: cover;
            height: 100vh;
            position: relative;
            font-family: sans-serif;
        }

        @keyframes driveIn {
            from {
                left: -700px;
            }

            to {
                left: 650px;
            }
        }

        @keyframes driveOutRight {
            from {
                left: 650px;
            }

            to {
                left: 1200px;
                opacity: 0;
            }
        }

        @keyframes driveBackIn {
            from {
                left: 1200px;
            }

            to {
                left: 650px;
            }
        }

        @keyframes driveBackLeft {
            from {
                left: 650px;
            }

            to {
                left: -700px;
                opacity: 0;
            }
        }

        @keyframes appearStudent {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes boardBus {
            to {
                opacity: 0;
                transform: translateY(-20px) scale(0.8);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #bus {
            position: absolute;
            bottom: 28px;
            left: -700px;
            width: 650px;
            height: auto;
            z-index: 10;
            transition: transform 0.2s;
        }

        #bus.flipped {
            transform: scaleX(-1);
        }

        #student-img {
            position: absolute;
            bottom: 150px;
            left: 525px;
            width: 150px;
            height: auto;
            opacity: 0;
            z-index: 12;
        }

        #message {
            position: absolute;
            top: 20px;
            left: 30%;
            transform: translateX(-50%);
            background-color: rgba(255, 245, 210, 0.7);
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 20px;
            font-weight: bold;
            color: #5c3b15;
            font-family: 'Segoe UI', sans-serif;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            opacity: 0;
            animation: fadeIn 1s ease-in-out 2.2s forwards;
            z-index: 50;
        }

        #speech-bubble {
            position: absolute;
            left: 550px;
            bottom: 370px;
            width: 200px;
            padding: 16px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
            font-size: 28px;
            font-weight: bold;
            color: #d45a00;
            font-family: 'Segoe UI', sans-serif;
            opacity: 0;
            animation: fadeIn 0.5s ease-in-out 2.5s forwards;
            transition: opacity 0.3s ease;
            z-index: 100;
            text-align: center;
        }

        #speech-bubble::after {
            content: "";
            position: absolute;
            bottom: -20px;
            left: 40px;
            border-width: 10px;
            border-style: solid;
            border-color: white transparent transparent transparent;
        }

        #bus-link {
            position: absolute;
            bottom: 28px;
            left: 0;
            width: 100vw;
            height: 280px;
            z-index: 30;
            background: transparent;
            cursor: pointer;
        }

        #bus-link:hover #bus {
            filter: brightness(1.07) drop-shadow(0 0 10px #f8be64aa);
            transition: filter 0.3s;
        }

        /* To prevent pointer events except on bus */
        #bus-link {
            pointer-events: none;
        }

        #bus-link.active {
            pointer-events: auto;
        }
    </style>
</head>

<body>
    <div id="bus-link">
        <img id="bus" src="bus.png" alt="bus" />
    </div>
    <img id="student-img" src="student.png" alt="student" />
    <div id="speech-bubble">VGtUtor???</div>
    <div id="message">Don't worry, click the bus and we will take you "Home"</div>

    <script>
        const bus = document.getElementById('bus');
        const student = document.getElementById('student-img');
        const bubble = document.getElementById('speech-bubble');
        const busLink = document.getElementById('bus-link');

        // Start: bus drives in
        bus.style.animation = 'driveIn 1.2s cubic-bezier(.6,1.5,.33,1) forwards';
        setTimeout(() => {
            // Student appears
            student.style.animation = 'appearStudent 0.5s ease-in-out forwards';
            setTimeout(() => {
                // Show speech bubble
                bubble.style.opacity = 1;
                // Enable clicking bus
                busLink.classList.add('active');
                // When click bus, start sequence
                busLink.onclick = function(e) {
                    e.preventDefault();
                    busLink.classList.remove('active');
                    // Hide bubble
                    bubble.style.transition = 'opacity 0.18s';
                    bubble.style.opacity = '0';
                    setTimeout(() => {
                        bubble.style.display = 'none';
                    }, 180);

                    // Student "steps out" (hide)
                    student.style.animation = 'boardBus 0.35s ease-in-out forwards';
                    // Bus drives out to right
                    setTimeout(() => {
                        bus.style.animation = 'driveOutRight 0.75s ease-in-out forwards';
                        // After bus left, flip direction and come back
                        setTimeout(() => {
                            bus.style.opacity = 0;
                            bus.style.transform = 'scaleX(-1)';
                            bus.style.left = '1200px';
                            // Animate drive back in
                            setTimeout(() => {
                                bus.style.opacity = 1;
                                bus.style.animation = 'driveBackIn 0.8s cubic-bezier(.6,1.5,.33,1) forwards';
                                // When bus is back, student boards again (hidden), then bus leaves to left, then redirect
                                setTimeout(() => {
                                    student.style.opacity = 0; // still hidden
                                    setTimeout(() => {
                                        bus.style.animation = 'driveBackLeft 0.7s cubic-bezier(.6,1.5,.33,1) forwards';
                                        setTimeout(() => {
                                            window.location.href = '../index.php'; // Redirect to home
                                        }, 700);
                                    }, 350);
                                }, 800);
                            }, 150);
                        }, 750);
                    }, 350);
                };
            }, 500); // after student appears
        }, 1200);
    </script>
</body>

</html>