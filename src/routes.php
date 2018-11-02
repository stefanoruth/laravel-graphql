<?php

Route::match(['GET', 'POST'], 'graphql', \Ruth\GraphQL\GraphQLController::class);
