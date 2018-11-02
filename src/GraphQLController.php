<?php

namespace Ruth\GraphQL;

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Illuminate\Http\Request;
use GraphQL\Type\Definition\Type;
use Illuminate\Routing\Controller;
use GraphQL\Type\Definition\ObjectType;
use Exception;

class GraphQLController extends Controller
{
    public function __invoke(Request $request)
    {
        // return response()->json($request->all());

        $queries = [
            'echo' => [
                'type' => Type::string(),
                'args' => [
                    'message' => Type::nonNull(Type::string()),
                ],
                'resolve' => function ($root, $args) {
                    return json_encode(compact('root', 'args'));
                }
            ],
            'echo2' => new ObjectType([
                'name' => 'Das ECHGGO',
                'fields' => [
                    'type' => Type::int(),
                ],
                'resolve' => function () {
                    return 'Hoooolæ';
                },
            ]),
        ];

        $mutations = [];

        try {
            return response()->json(
                GraphQL::executeQuery(
                    new Schema([
                        'query' => new ObjectType([
                            'name' => 'Query',
                            'fields' => $queries,
                        ]),
                        'mutation' => new ObjectType([
                            'name' => 'Mutation',
                            'fields' => $mutations,
                        ]),
                    ]),
                    $request->get('query'),
                    null,
                    null,
                    $request->get('variables'),
                    $request->get('operationName')
                )->toArray()
            );
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    [
                        'message' => $e->getMessage()
                    ]
                ]
            ]);
        }
    }
}
