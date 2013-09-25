<?php

interface CollectorSettings {
    function get();
    function update($new_settings);
    function save();
}
