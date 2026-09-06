<?php
echo '<div id="loader" class="loader-overlay">
    <div class="cell">
        <div class="card">
            <span class="flower-loader">Loading…</span>
        </div>
    </div>
</div>
<style>
    .loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .flower-loader {
        overflow: hidden;
        position: relative;
        text-indent: -9999px;
        display: inline-block;
        width: 16px;
        height: 16px;
        background: #e96;
        border-radius: 100%;
        -moz-box-shadow: white 0 0 15px 0, #485 -12px -12px 0 4px,
            #485 12px -12px 0 4px, #485 12px 12px 0 4px, #485 -12px 12px 0 4px;
        -webkit-box-shadow: white 0 0 15px 0, #485 -12px -12px 0 4px,
            #485 12px -12px 0 4px, #485 12px 12px 0 4px, #485 -12px 12px 0 4px;
        box-shadow: white 0 0 15px 0, #485 -12px -12px 0 4px, #485 12px -12px 0 4px,
            #485 12px 12px 0 4px, #485 -12px 12px 0 4px;
        -moz-animation: flower-loader 5s infinite ease-in-out;
        -webkit-animation: flower-loader 5s infinite ease-in-out;
        animation: flower-loader 5s infinite ease-in-out;
        -moz-transform-origin: 50% 50%;
        -ms-transform-origin: 50% 50%;
        -webkit-transform-origin: 50% 50%;
        transform-origin: 50% 50%;
    }
    @-moz-keyframes flower-loader {
        0% {
            -moz-transform: rotate(0deg);
            transform: rotate(0deg);
            -moz-box-shadow: white 0 0 15px 0, #485 -12px -12px 0 4px,
                #485 12px -12px 0 4px, #485 12px 12px 0 4px, #485 -12px 12px 0 4px;
            box-shadow: white 0 0 15px 0, #485 -12px -12px 0 4px, #485 12px -12px 0 4px,
                #485 12px 12px 0 4px, #485 -12px 12px 0 4px;
        }
        50% {
            -moz-transform: rotate(1080deg);
            transform: rotate(1080deg);
            -moz-box-shadow: white 0 0 15px 0, #485 12px 12px 0 4px,
                #485 -12px 12px 0 4px, #485 -12px -12px 0 4px, #485 12px -12px 0 4px;
            box-shadow: white 0 0 15px 0, #485 12px 12px 0 4px, #485 -12px 12px 0 4px,
                #485 -12px -12px 0 4px, #485 12px -12px 0 4px;
        }
    }
    @-webkit-keyframes flower-loader {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
            -webkit-box-shadow: white 0 0 15px 0, #485 -12px -12px 0 4px,
                #485 12px -12px 0 4px, #485 12px 12px 0 4px, #485 -12px 12px 0 4px;
            box-shadow: white 0 0 15px 0, #485 -12px -12px 0 4px, #485 12px -12px 0 4px,
                #485 12px 12px 0 4px, #485 -12px 12px 0 4px;
        }
        50% {
            -webkit-transform: rotate(1080deg);
            transform: rotate(1080deg);
            -webkit-box-shadow: white 0 0 15px 0, #485 12px 12px 0 4px,
                #485 -12px 12px 0 4px, #485 -12px -12px 0 4px, #485 12px -12px 0 4px;
            box-shadow: white 0 0 15px 0, #485 12px 12px 0 4px, #485 -12px 12px 0 4px,
                #485 -12px -12px 0 4px, #485 12px -12px 0 4px;
        }
    }
    @keyframes flower-loader {
        0% {
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg);
            -moz-box-shadow: white 0 0 15px 0, #485 -12px -12px 0 4px,
                #485 12px -12px 0 4px, #485 12px 12px 0 4px, #485 -12px 12px 0 4px;
            -webkit-box-shadow: white 0 0 15px 0, #485 -12px -12px 0 4px,
                #485 12px -12px 0 4px, #485 12px 12px 0 4px, #485 -12px 12px 0 4px;
            box-shadow: white 0 0 15px 0, #485 -12px -12px 0 4px, #485 12px -12px 0 4px,
                #485 12px 12px 0 4px, #485 -12px 12px 0 4px;
        }
        50% {
            -moz-transform: rotate(1080deg);
            -ms-transform: rotate(1080deg);
            -webkit-transform: rotate(1080deg);
            transform: rotate(1080deg);
            -moz-box-shadow: white 0 0 15px 0, #485 12px 12px 0 4px,
                #485 -12px 12px 0 4px, #485 -12px -12px 0 4px, #485 12px -12px 0 4px;
            -webkit-box-shadow: white 0 0 15px 0, #485 12px 12px 0 4px,
                #485 -12px 12px 0 4px, #485 -12px -12px 0 4px, #485 12px -12px 0 4px;
            box-shadow: white 0 0 15px 0, #485 12px 12px 0 4px, #485 -12px 12px 0 4px,
                #485 -12px -12px 0 4px, #485 12px -12px 0 4px;
        }
    }
    .cell {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .card {
        padding: 20px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>';
?>