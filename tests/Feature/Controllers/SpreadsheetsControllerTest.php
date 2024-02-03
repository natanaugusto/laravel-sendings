<?php
it('has index page to list spreadsheets', function () {
    $response = $this->get(route('spreadsheets.index'));
    $response->assertStatus(302);
});
