<?php

test('home redirects guests to the login page', function () {
    $this->get(route('home'))->assertRedirect(route('dashboard', absolute: false));

    $this->get(route('dashboard'))->assertRedirect(route('login'));
});
