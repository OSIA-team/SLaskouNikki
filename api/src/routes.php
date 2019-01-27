<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

/**
 * DB:
 * host: md28.wedos.net
 * username: a196442_api
 * password: 33fFCTxL
 * database: d196442_api
 * type: MySQL
 *
 * TABLES
 * _____________________________
 * article | id:
 *         | title:
 *         | content:
 *         | title_pic:
 *         | public_time
 *         | created_time
 * _____________________________
 *
 */


$app->post('/article/update/{id}', function (Request $request, Response $response, $args){
    $data = $request->getParsedBody();
    $id = (int) $args['id'];

    $articleData['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING);
    $articleData['content'] = filter_var($data['content'], FILTER_SANITIZE_STRING);
    $articleData['title_pic'] = filter_var($data['title_pic'], FILTER_SANITIZE_STRING);
    $articleData['public_date'] = filter_var($data['public_date'], FILTER_SANITIZE_STRING) ?? null;

    $this->dibi->query("UPDATE article SET %a WHERE id = %i", $articleData, $id);
    $affectedRows = $this->dibi->getAffectedRows();
    if ($affectedRows){
        $responseData = $response->withStatus(200, 'success');
    } else {
        $responseData = $response->withStatus(500, 'fail');
    }
    return $responseData;
});


$app->get('/article/{id}', function (Request $request, Response $response, $args){
    $id = (int) $args['id'];
    $article = $this->dibi->query("SELECT * FROM article WHERE id = {$id}")->fetchAll();
    return $response->withJson($article, 200);
});

$app->get('/article', function(Request $request, Response $response){
    $articles = $this->dibi->query("SELECT * FROM article")->fetchAll();
    if ($articles){
        $responseData = $response->withJson($articles, 200);
    } else {
        $responseData = $response->withStatus(204);
    }
    return $responseData;
});

$app->post('/article/new', function (Request $request, Response $response, $args){
    $data = $request->getParsedBody();
    $articleData = array();
    $articleData['title'] = filter_var($data['title'], FILTER_SANITIZE_STRING) ?? '';
    $articleData['content'] = filter_var($data['content'], FILTER_SANITIZE_STRING) ?? '';
    $articleData['title_pic'] = filter_var($data['title_pic'], FILTER_SANITIZE_STRING) ?? '';
    $articleData['public_date'] = filter_var($data['public_date'], FILTER_SANITIZE_STRING) ?? null;
    $this->dibi->query("INSERT INTO article ", $articleData);
    $affectedRows = $this->dibi->getAffectedRows();
    if ($affectedRows){
        $responseData = $response->withStatus(201, 'success');
    } else {
        $responseData = $response->withStatus(500, 'fail');
    }
    return $responseData;
});

$app->get('/test', function(){
    return "test";
});
