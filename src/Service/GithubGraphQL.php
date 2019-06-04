<?php


namespace App\Service;


class GithubGraphQL
{
    // récupérer les infos d'un profil github
    public function getProfileInfo($login){
        // requête graphql nommée GetUser et stockée dans la variable $query, qui prend en paramètre obligatoirement un chaîne de caractère (variable $user):
        $query = <<<'GRAPHQL'
query GetUser($user: String!) {
   user (login: $user) {
    name
    email
    avatarUrl
    bio
    repositoriesContributedTo {
      totalCount
    }
  }
}
GRAPHQL;

        $datas = $this->graphql_query('https://api.github.com/graphql', $query, ['user' => $login], '61d1ff4e3bf6e7940263bedd60e9c43cd8f7feb0');

        return $datas;

    }

    // fonction privée qui ne peut être appelée en dehors de la classe
    private function graphql_query(string $endpoint, string $query, array $variables = [], ?string $token = null): array
    {
        $headers = ['Content-Type: application/json', 'User-Agent: Dunglas\'s minimal GraphQL client'];
        if (null !== $token) {
            $headers[] = "Authorization: bearer $token";
        }
        if (false === $data = @file_get_contents($endpoint, false, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => $headers,
                    'content' => json_encode(['query' => $query, 'variables' => $variables]),
                ]
            ]))) {
            $error = error_get_last();
            throw new \ErrorException($error['message'], $error['type']);
        }
        // json_decode permet de transformer en objet ou tableau associatif (deuxième paramètre à true donc ici le json sera transformé en tableau associatif :
        return json_decode($data, true);
    }

}