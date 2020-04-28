# アーキテクチャ

#### Client
メイン。  
Service使った処理を書く

#### Interpreter
(非PMMP側)
Listenerから受けた内容を処理しClientとやり取りする。

#### Client(PMMP側)
pmmpに関した処理を行う

#### Service(非PMMP側)
非PMMP側
ステートレスな処理
基本的な処理はここで行われる。  
細かな条件分離、変換とか。

#### Repository(非PMMP側)
DBとのやりとりのみ行う。  
Serviceからしか呼び出されない。

#### Model
value_objectとentityに分かれる

## 依存関係
依存関係は矢印のみ許可する

Listener       
↓ ↓  
↓ Interpreter  
↓ ↓↓  
↓ ↓Client(こいつはサービスと関係を持たない)  
↓ ↓  
Service  
↓  
Repository  
