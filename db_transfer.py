#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Created on Mon May 13 16:00:58 2019

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
        database = 'utterfare')

remote_cursor = remote_db.cursor()
local_cursor = local_db.cursor()

sql = "SELECT COMPANY_ID, ITEM_NAME, ITEM_DESCRIPTION, ITEM_IMAGE FROM 290sc_items"

remote_cursor.execute(sql)

results = remote_cursor.fetchall()

for row in results:

    menu_items_sql = 'INSERT INTO menu_items(item_name, item_description, item_short_description, vendor_id, primary_image)\
    VALUES("%s", "%s", "%s", %i, "%s")'

    print(menu_items_sql % (row[1].strip().replace("'", "\'").replace('"', '\"'), row[2].strip().replace("'", "\'").replace('"', '\"'), row[2].strip().replace("'", "\'").replace('"', '\"'), row[0], row[3].strip().replace("'", "\'").replace('"', '\"')))
    local_cursor.execute(menu_items_sql % (row[1].strip().replace("'", "\'").replace('"', '\"'), row[2].strip().replace("'", "\'").replace('"', '\"'), row[2].strip().replace("'", "\'").replace('"', '\"'), row[0], row[3].strip().replace("'", "\'").replace('"', '\"')))
    
    local_db.commit()    
    