# アーキテクチャ

#### Main
イベントを受け受け取りListenerに流す

#### Listener
ServiceとInterpreterをまとめる役割がある。
簡単な処理を行う

#### Interpreter(非PMMP側)
Listenerから受けた内容を処理しClientとやり取りする。

#### Client(PMMP側)
PMMPモデルに書くと肥大化シそうな処理を行う

#### Service(非PMMP側)
ステートレスな処理
非PMMP側の基本的な処理はここで行われる。  

#### Repository(非PMMP側)
DBとのやりとりのみ行う。  
Serviceからしか呼び出されない。

#### Model
value_objectとentityに分かれる

#### PMMP model

## 依存関係
依存関係は矢印のみ許可する  

Main  
↓  
Listener       
↓ ↓  
↓ Interpreter  
↓ ↓↓  
↓ ↓Client(こいつはサービスと関係を持たない)  
↓ ↓  
Service  
↓  
Repository  
