<?php

use AGerault\Framework\Validation\Rules\EmailRule;

it(
    "should pass the email test",
    function () {
        $rule = new EmailRule("gerault-alexandre@orange.fr");

        expect($rule->validate())->toBeTrue();
    }
);

it(
    "should not pass the email test when we do not provide a correct email",
    function () {
        $rule = new EmailRule("1234");

        expect($rule->validate())->toBeFalse();
    }
);

it(
    "should give the error message",
    function () {
        $rule = new EmailRule("1234");

        expect($rule->onFailMessage())->toBe("1234 is not a valid email address");
    }
);
