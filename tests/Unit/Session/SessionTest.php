<?php

use AGerault\Framework\Contracts\Session\SessionInterface;

it("should be able to be used like an array", function () {
   $session = new AGerault\Framework\Session\Session();

   $session['test'] = "blabla";
   expect($session)->toBeInstanceOf(SessionInterface::class);
   expect($session['test'])->toBe("blabla");

   unset($session['test']);
   expect(isset($session['test']))->toBeFalse();
});

it("should be able to be cleared", function () {
   $session = new AGerault\Framework\Session\Session();

   $session->put('something', 1);
   expect($session->get('something'))->toBe(1);
});
