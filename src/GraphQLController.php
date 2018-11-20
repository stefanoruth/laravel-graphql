<?php

namespace Ruth\GraphQL;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ruth\GraphQL\GraphQL;

class GraphQLController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->has('debug')) {
            dd(
                GraphQL::$types,
                GraphQL::$queries,
                GraphQL::$mutations,
                $request->all()
            );
        }

        return response()->json(
            GraphQL::executeQuery(
                $request->get('query'),
                $request->get('variables'),
                $request->get('operationName')
            )
        );
    }
}
