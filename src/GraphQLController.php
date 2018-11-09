<?php

namespace Ruth\GraphQL;

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Illuminate\Http\Request;
use GraphQL\Type\Definition\Type;
use Illuminate\Routing\Controller;
use GraphQL\Type\Definition\ObjectType;
use Exception;
use Ruth\GraphQL\UserType;
use Ruth\GraphQL\ListUsersQuery;
use Illuminate\Support\Collection;
use Ruth\GraphQL\GraphQL as Kernel;

class GraphQLController extends Controller
{
    protected function formatQuery($queries)
    {
        return Collection::make($queries)->flatMap(function ($item) {
            return $item;
        })->toArray();
    }

    public function __invoke(Request $request)
    {
        $types = Kernel::loadTypes([
            UserType::class,
        ]);

        $queries = Kernel::loadQueries([
            ListUsersQuery::class,
            ShowUserQuery::class,
        ]);

        $mutations = Kernel::loadMutations([]);


        if ($request->has('debug')) {
            dd($types, $queries, $mutations, $request->all());
        }

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
