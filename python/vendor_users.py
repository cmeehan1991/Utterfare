#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Created on Tue Jul 23 15:31:21 2019

@author: cmeehan
"""

import mysql.connector

remote_db = mysql.connector.connect(
        host = 'ns8140.hostgator.com',
        user = 'cmeehan',
        passwd = 'Ilovetaylor2016!',
        database="cmeehan_utterfare")

local_db = mysql.connector.connect(
        host = 'localhost', 
        user = 'root', 
        passwd = 'root', 
        database = 'utterfare', )

remote_cursor = remote_db.cursor()
local_cursor = local_db.cursor()

def get_new_vendor_id(legacy_id, legacy_table):
    
    sql = "SELECT m1.vendor_id FROM vendor_meta m1 \
    INNER JOIN vendor_meta m2 ON m2.vendor_id = m1.vendor_id \
    WHERE (m1.meta_keyword = '_legacy_table' AND m1.meta_value = '%s') and (m2.meta_keyword = '_legacy_id' AND m2.meta_value = '%s')"
    
    local_cursor.execute(sql % (legacy_table, legacy_id))
    
    results = local_cursor.fetchall()
    
    if results != None:
        return results[0][0]
    
    
def insert_vendor_user(vendor_id, username):
    sql = 'INSERT INTO vendor_users(username, vendor_id, password) VALUES("%s", %i, MD5("%s"))'
        
    try:
        local_cursor.execute(sql % (username, vendor_id, "password"))
        local_db.commit()
                
    except mysql.connector.Error as err:
        print("something went wrong: {}".format(err))
        
    

sql = "SELECT ID, USERNAME, COMPANY_ID, DATA_TABLE \
FROM cmeehan_utterfare_20190703.VENDOR_LOGIN \
WHERE USERNAME != 'Utter2015' AND DATA_TABLE = '270NC'"

remote_cursor.execute(sql)

results = remote_cursor.fetchall()

for row in results:
    new_vendor_id = get_new_vendor_id(row[2], row[3])
    
    insert_vendor_user(new_vendor_id, row[1])

    
print("done")

