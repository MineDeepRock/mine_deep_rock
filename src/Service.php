<?php


//わかりやすくするため、サービスは継承する
/*
モデルのメソッドで実装するのが相応しくない場合や
メソッドだと肥大化しそうな場合につかう。
使いすぎるとドメインモデル貧血症
 *  */
abstract class Service {}