# アーキテクチャ

#### Client
メイン。  
Service使った処理を書く

#### Service
基本的な処理はここで行われる。  
細かな条件分離、変換とか。

#### Repository
DBとのやりとりのみ行う。  
Serviceからしか呼び出されない。

#### Model
value_objectとentityに分かれる

## 依存関係
依存関係は上から下のみ許す。

Client  
↓  
Service  
↓  
Repository  
