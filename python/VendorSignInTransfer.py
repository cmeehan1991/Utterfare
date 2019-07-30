import mysql.connector

remote_db = mysql.connector.connect(
        host = 'ns8140.hostgator.com',
        user = 'cmeehan',
        passwd = 'Ilovetaylor2016!',
        database="cmeehan_utterfare_20190703")

local_db = mysql.connector.connect(
        host = 'localhost', 
        user = 'root', 
        passwd = 'root', 
        database = 'utterfare')


remote_cursor = remote_db.cursor()
local_cursor = local_db.cursor()

sql = "SELECT ID, USERNAME, FIRST_NAME, LAST_NAME, COMPANY_ID FROM VENDOR_LOGIN"

remote_cursor.execute(sql)

results = remote_cursor.fetchall()

for row in results:
    print(row)
