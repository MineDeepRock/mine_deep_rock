# MINE DEEP ROCK
# Architecture
## System
部分ごとの実装をSystemに分け、それをメインのSystemが使う  

## Service
ステートレスな処理のみを行う

## Repository
ステートレスなDBとのやり取りを行う  
get update deleteなどの簡単な処理のみを行う

## Controller
部分ごとの実装を担当する  
他のSystemを利用したりする  
ListenerやInterpreterから利用される

## Interpreter
ItemやEntityのクラスに含まれる  
基本、ItemはEntityに直接処理を書かないので、こいつを使う

## Client
PMMPのみの処理を行う  
ItemやEntityに書くと肥大化する場合に使う  
ステートレスが望ましい

## Model
ここではDDDのモデルを指す  
PHPではEntityとValueObjectの区別が難しいため、そこまで意識していない