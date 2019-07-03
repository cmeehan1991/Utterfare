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

sql = "SELECT ID, COMPANY_ID, ITEM_NAME, ITEM_DESCRIPTION, ITEM_IMAGE FROM 300ga_items"

remote_cursor.execute(sql)

results = remote_cursor.fetchall()

for row in results:
    item_image = ("http://www.utterfare.com/images/300ga_images/" + str(row[1]) + "_" + str(row[0]) + ".png")
    
    vendor_id_sql = "SELECT A.vendor_id FROM vendor_meta A INNER JOIN vendor_meta B ON B.vendor_id = A.vendor_id WHERE A.meta_value = %i AND B.meta_value = '%s';"
    
    local_cursor.execute(vendor_id_sql % (row[1], '300ga'))
    
    results = local_cursor.fetchall()
    
    vendor_id = 0
    for vendor_meta in results:
        vendor_id = vendor_meta[0]    
     
    menu_items_sql = 'INSERT INTO menu_items(item_name, item_description, item_short_description, vendor_id, primary_image)\
    VALUES("%s", "%s", "%s", %i, "%s")'

    local_cursor.execute(menu_items_sql % 
                         (row[2].strip().replace("'", "\'").replace('"', '\"'), 
                          row[3].strip().replace("'", "\'").replace('"', '\"'), 
                          row[3].strip().replace("'", "\'").replace('"', '\"'), 
                          vendor_id, 
                          item_image))
    
    local_db.commit()    
    