# チームシステム
#### アーキテクチャ   
command  
↓  
service  
(条件分岐など、細かく)  
(pmmpとのやり取りはしない)  
↓  
repository  
(DBとのやり取り、簡単に)  
(pmmpとのやり取りはしない)  

#### DB構成
###### teams  
- id(varchar(60))
- owner_name(varchar(60))
- first_coworker_name(varchar(60))
- second_coworker_name(varchar(60))
- third_coworker_name(varchar(60))